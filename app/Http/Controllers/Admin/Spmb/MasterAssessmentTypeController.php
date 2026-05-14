<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\MasterAssessmentTypeRepositoryInterface;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterAssessmentTypeController extends Controller
{
    public function __construct(
        protected MasterAssessmentTypeRepositoryInterface $repository
    ) {}

    /**
     * Tampilan utama halaman Master Jenis Penilaian.
     */
    public function index()
    {
        return view('admin.spmb.master.jenis_penilaian.index');
    }

    /**
     * Mengambil data untuk DataTables Server-side.
     */
    public function getData()
    {
        $data = $this->repository->all();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check">
                            <input class="form-check-input check-item" type="checkbox" value="'.$row->id.'">
                        </div>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown">
                            <button class="btn btn-sm btn-light-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item btn-detail" href="javascript:void(0)" data-id="'.$row->id.'"><i class="ti ti-eye me-2"></i>Detail</a></li>
                                <li><a class="dropdown-item btn-edit" href="javascript:void(0)" data-id="'.$row->id.'"><i class="ti ti-edit me-2"></i>Edit</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger btn-delete" href="javascript:void(0)" data-id="'.$row->id.'"><i class="ti ti-trash me-2"></i>Hapus</a></li>
                            </ul>
                        </div>';
            })
            ->editColumn('input_type', function ($row) {
                $badges = [
                    'score' => '<span class="badge bg-light-info text-info">Angka (Score)</span>',
                    'pass_fail' => '<span class="badge bg-light-warning text-warning">Lulus/Gagal</span>'
                ];
                return $badges[$row->input_type] ?? $row->input_type;
            })
            ->addColumn('status', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '<div class="form-check form-switch">
                            <input class="form-check-input toggle-status" type="checkbox" data-id="'.$row->id.'" '.$checked.'>
                        </div>';
            })
            ->rawColumns(['checkbox', 'input_type', 'status', 'action'])
            ->make(true);
    }

    /**
     * Menyimpan data baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:master_assessment_types,name',
            'input_type' => 'required|in:score,pass_fail',
            'description' => 'nullable',
            'is_active' => 'boolean'
        ]);

        try {
            $data = $validated;
            $data['is_active'] = $request->boolean('is_active', true);
            $data['created_by'] = auth()->id();

            $this->repository->create($data);

            return response()->json(['message' => 'Jenis Penilaian berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil detail data.
     */
    public function show($id)
    {
        $data = $this->repository->find($id, ['*'], ['creator', 'updater']);
        return response()->json($data);
    }

    /**
     * Memperbarui data.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:100|unique:master_assessment_types,name,' . $id,
            'input_type' => 'required|in:score,pass_fail',
            'description' => 'nullable',
            'is_active' => 'boolean'
        ]);

        try {
            $data = $validated;
            $data['is_active'] = $request->boolean('is_active');
            $data['updated_by'] = auth()->id();

            $this->repository->update($id, $data);

            return response()->json(['message' => 'Jenis Penilaian berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data'], 500);
        }
    }

    /**
     * Menghapus data.
     */
    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'Jenis Penilaian berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }

    /**
     * Toggle status aktif.
     */
    public function toggleStatus($id)
    {
        try {
            $model = $this->repository->find($id);
            $this->repository->update($id, ['is_active' => !$model->is_active]);
            return response()->json(['message' => 'Status berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengubah status'], 500);
        }
    }

    /**
     * Bulk update status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:master_assessment_types,id',
            'status' => 'required|boolean'
        ]);

        try {
            \App\Models\MasterAssessmentType::whereIn('id', $validated['ids'])->update([
                'is_active' => $validated['status'],
                'updated_by' => auth()->id()
            ]);

            return response()->json(['message' => 'Status massal berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui status massal'], 500);
        }
    }
}
