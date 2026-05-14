<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $repository,
        protected RoleRepositoryInterface $roleRepository
    ) {}

    public function index()
    {
        $roles = $this->roleRepository->all();
        return view('admin.settings.user.index', compact('roles'));
    }

    public function getData(Request $request)
    {
        $query = $this->repository->query(['roles']);

        // Filter berdasarkan Role jika ada
        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user_info', function ($row) {
                return '<div class="d-flex align-items-center">
                            <div class="avtar avtar-s bg-light-primary text-primary me-2">
                                <i class="ti ti-user fs-4"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block">' . $row->name . '</span>
                                <span class="text-muted small">' . $row->email . ' | @' . $row->username . '</span>
                            </div>
                        </div>';
            })
            ->addColumn('roles', function ($row) {
                $badges = '';
                foreach ($row->roles as $role) {
                    $badges .= '<span class="badge bg-light-info text-info me-1">' . $role->name . '</span>';
                }
                return $badges ?: '<span class="text-muted small">-</span>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown">
                            <button class="btn btn-sm btn-light-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item btn-edit" href="javascript:void(0)" data-id="'.$row->id.'"><i class="ti ti-edit me-2 text-primary"></i>Edit User</a></li>
                                <li><a class="dropdown-item btn-reset-password" href="javascript:void(0)" data-id="'.$row->id.'"><i class="ti ti-key me-2 text-warning"></i>Reset Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger btn-delete" href="javascript:void(0)" data-id="'.$row->id.'"><i class="ti ti-trash me-2"></i>Hapus</a></li>
                            </ul>
                        </div>';
            })
            ->rawColumns(['user_info', 'roles', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'roles' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $user = $this->repository->create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user->syncRoles($request->roles);

            DB::commit();
            return response()->json(['message' => 'User berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menambahkan user: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $user = $this->repository->find($id, ['*'], ['roles']);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->toArray()
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'roles' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $user = $this->repository->find($id);
            $data = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $user->syncRoles($request->roles);

            DB::commit();
            return response()->json(['message' => 'User berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui user'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'User berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus user'], 500);
        }
    }

    /**
     * Reset password user ke default (password).
     */
    public function resetPassword($id)
    {
        try {
            $user = $this->repository->find($id);
            $user->update([
                'password' => Hash::make('password')
            ]);
            return response()->json(['message' => 'Password berhasil di-reset ke: password']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal me-reset password'], 500);
        }
    }
}
