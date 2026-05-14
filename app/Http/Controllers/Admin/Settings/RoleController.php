<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct(
        protected RoleRepositoryInterface $repository
    ) {
    }

    /**
     * Tampilkan halaman utama Manajemen Role.
     */
    public function index()
    {
        $scopes = \App\Enums\RoleScope::cases();
        return view('admin.settings.rbac.index', compact('scopes'));
    }

    /**
     * Ambil data roles untuk ditampilkan di tabel.
     */
    public function getData(Request $request)
    {
        $query = $this->repository->query();

        // Filter berdasarkan Scope jika ada
        if ($request->filled('scope') && $request->scope !== 'all') {
            $query->where('scope', $request->input('scope'));
        }

        return \Yajra\DataTables\Facades\DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('role_name', function ($row) {
                $scopeLabel = $row->scope instanceof \App\Enums\RoleScope ? $row->scope->label() : 'General';
                return '<div class="d-flex align-items-center">
                            <div class="avtar avtar-s bg-light-primary text-primary me-2">
                                <i class="ti ti-user-check fs-4"></i>
                            </div>
                            <div>
                                <span class="fw-bold d-block">' . $row->name . '</span>
                                <span class="text-muted small">Scope: ' . $scopeLabel . '</span>
                            </div>
                        </div>';
            })
            ->addColumn('permissions', function ($row) {
                $perms = $row->permissions->pluck('name');
                $html = '<div class="d-flex flex-wrap gap-1">';
                foreach ($perms->take(3) as $perm) {
                    $html .= '<span class="badge bg-light-info text-info">' . $perm . '</span>';
                }
                if ($perms->count() > 3) {
                    $html .= '<span class="badge bg-light-secondary text-secondary">+' . ($perms->count() - 3) . ' lainnya</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown">
                            <button class="btn btn-sm btn-light-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="' . route('admin.settings.rbac.edit', $row->id) . '">
                                        <i class="ti ti-edit me-2 text-primary"></i>Edit Role
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger btn-delete" href="javascript:void(0)" data-id="' . $row->id . '">
                                        <i class="ti ti-trash me-2"></i>Hapus
                                    </a>
                                </li>
                            </ul>
                        </div>';
            })
            ->rawColumns(['role_name', 'permissions', 'action'])
            ->make(true);
    }

    /**
     * Ambil daftar semua permission yang dikelompokkan berdasarkan modul.
     */
    public function getPermissions()
    {
        $grouped = $this->repository->getGroupedPermissions();
        return response()->json(['data' => $grouped]);
    }

    /**
     * Tampilkan halaman tambah role.
     */
    public function create()
    {
        $scopes = \App\Enums\RoleScope::cases();
        return view('admin.settings.rbac.create', compact('scopes'));
    }

    /**
     * Sinkronisasi permission dari routes ke database.
     */
    public function syncPermissions()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('app:sync-permissions --prune');
            return response()->json(['message' => 'Sinkronisasi permission berhasil']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal sinkronisasi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Simpan role baru beserta permissionnya.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'scope' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\RoleScope::class)],
            'permissions' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $role = $this->repository->create([
                'name' => $request->name,
                'scope' => $request->scope,
                'guard_name' => 'web'
            ]);

            if ($request->has('permissions')) {
                $this->repository->syncPermissions($role->id, $request->permissions);
            }

            DB::commit();
            return response()->json(['message' => 'Role berhasil dibuat']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat role: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Tampilkan halaman edit role.
     */
    public function edit($id)
    {
        $scopes = \App\Enums\RoleScope::cases();
        return view('admin.settings.rbac.create', compact('id', 'scopes'));
    }

    /**
     * Ambil detail role untuk form edit.
     */
    public function show($id)
    {
        $role = $this->repository->find($id, ['*'], ['permissions']);
        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'scope' => $role->scope->value ?? $role->scope,
            'permissions' => $role->permissions->pluck('name')->toArray()
        ]);
    }

    /**
     * Perbarui data role dan permissionnya.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'scope' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\RoleScope::class)],
            'permissions' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $this->repository->update($id, [
                'name' => $request->name,
                'scope' => $request->scope
            ]);

            $this->repository->syncPermissions($id, $request->permissions ?? []);

            DB::commit();
            return response()->json(['message' => 'Role berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui role'], 500);
        }
    }

    /**
     * Hapus role dari sistem.
     */
    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'Role berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus role'], 500);
        }
    }
}
