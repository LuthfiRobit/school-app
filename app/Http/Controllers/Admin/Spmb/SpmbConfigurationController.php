<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Repositories\Interfaces\SpmbConfigurationRepositoryInterface;
use App\Repositories\Interfaces\AcademicYearRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class SpmbConfigurationController extends Controller
{
    public function __construct(
        protected SpmbConfigurationRepositoryInterface $repository,
        protected AcademicYearRepositoryInterface $academicYearRepository
    ) {
    }

    /**
     * Tampilan utama halaman Konfigurasi SPMB.
     */
    public function index()
    {
        return view('admin.spmb.configurations.index');
    }

    /**
     * Mengambil data untuk DataTables Server-side.
     */
    public function getData()
    {
        $data = $this->academicYearRepository->all(['*'], ['spmbConfiguration']);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-sm btn-light-primary rounded-pill btn-setup" data-id="' . $row->id . '">
                            <i class="ti ti-settings"></i> Setup
                        </button>';
            })
            ->addColumn('academic_year', function ($row) {
                return '<span class="badge bg-light-primary text-primary">' . $row->name . '</span>';
            })
            ->addColumn('academic_period', function ($row) {
                return \Carbon\Carbon::parse($row->start_date)->format('M Y') . ' - ' . \Carbon\Carbon::parse($row->end_date)->format('M Y');
            })
            ->addColumn('spmb_period', function ($row) {
                if ($row->spmbConfiguration) {
                    return \Carbon\Carbon::parse($row->spmbConfiguration->reg_start_date)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($row->spmbConfiguration->reg_end_date)->format('d M Y');
                }
                return '<span class="text-muted fst-italic">Belum disetup</span>';
            })
            ->addColumn('total_quota', function ($row) {
                return $row->spmbConfiguration ? $row->spmbConfiguration->total_quota . ' Siswa' : '-';
            })
            ->addColumn('status', function ($row) {
                if ($row->spmbConfiguration) {
                    $statusClass = [
                        'draft' => 'bg-light-secondary',
                        'active' => 'bg-light-success',
                        'closed' => 'bg-light-warning',
                        'archived' => 'bg-light-dark'
                    ][$row->spmbConfiguration->status] ?? 'bg-light-secondary';

                    $statusLabel = [
                        'draft' => 'DRAFT',
                        'active' => 'AKTIF',
                        'closed' => 'DITUTUP',
                        'archived' => 'ARSIP'
                    ][$row->spmbConfiguration->status] ?? strtoupper($row->spmbConfiguration->status);

                    return '<span class="badge ' . $statusClass . '">' . $statusLabel . '</span>';
                }
                return '<span class="badge bg-light-danger">BELUM DIATUR</span>';
            })
            ->rawColumns(['action', 'academic_year', 'spmb_period', 'status'])
            ->make(true);
    }

    /**
     * Mengambil detail konfigurasi untuk Modal.
     */
    public function show($id)
    {
        $academicYear = $this->academicYearRepository->find($id, ['*'], ['spmbConfiguration']);
        return response()->json([
            'academicYear' => $academicYear,
            'spmbConfig' => $academicYear->spmbConfiguration
        ]);
    }

    /**
     * Menyimpan atau memperbarui konfigurasi SPMB.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'reg_start_date' => 'required|date',
            'reg_end_date' => 'required|date|after_or_equal:reg_start_date',
            'total_quota' => 'required|integer|min:1',
            'status' => 'required|in:draft,active,closed,archived',
        ]);

        DB::beginTransaction();
        try {
            $spmbConfig = $this->repository->findByAcademicYear($id);

            if ($spmbConfig) {
                $this->repository->update($spmbConfig->id, $validated);
            } else {
                $validated['academic_year_id'] = $id;
                $this->repository->create($validated);
            }

            DB::commit();
            return response()->json([
                'message' => 'Konfigurasi SPMB berhasil disimpan'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
