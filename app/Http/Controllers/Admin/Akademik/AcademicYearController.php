<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\AcademicYearRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AcademicYearController extends Controller
{
    public function __construct(
        protected AcademicYearRepositoryInterface $repository
    ) {}

    /**
     * Tampilan utama halaman Tahun Pelajaran.
     */
    public function index()
    {
        return view('admin.akademik.tahun_ajaran.index');
    }

    /**
     * Mengambil data untuk DataTables Server-side.
     */
    public function getData()
    {
        $data = $this->repository->all();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('period', function ($row) {
                return $row->start_date->format('d M Y') . ' - ' . $row->end_date->format('d M Y');
            })
            ->addColumn('status', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '<div class="form-check form-switch">
                            <input class="form-check-input toggle-status" type="checkbox" data-id="'.$row->id.'" '.$checked.'>
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
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Menyimpan data Tahun Pelajaran baru beserta semesternya.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'ganjil_start_date' => 'required|date',
            'ganjil_end_date' => 'required|date|after:ganjil_start_date',
            'genap_start_date' => 'required|date',
            'genap_end_date' => 'required|date|after:genap_start_date',
            'semester_active' => 'required|in:ganjil,genap'
        ]);

        try {
            DB::beginTransaction();

            // 1. Jika tahun ajaran baru diatur aktif, nonaktifkan yang lain
            if ($request->has('is_active')) {
                \App\Models\AcademicYear::where('is_active', true)->update(['is_active' => false]);
                \App\Models\Semester::where('is_active', true)->update(['is_active' => false]);
            }

            // 2. Create Academic Year
            $academicYear = $this->repository->create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => $request->has('is_active'),
                'created_by' => auth()->id()
            ]);

            // 3. Create Semesters
            $academicYear->semesters()->create([
                'type' => 'ganjil',
                'start_date' => $validated['ganjil_start_date'],
                'end_date' => $validated['ganjil_end_date'],
                'is_active' => ($validated['semester_active'] === 'ganjil' && $request->has('is_active')),
                'created_by' => auth()->id()
            ]);

            $academicYear->semesters()->create([
                'type' => 'genap',
                'start_date' => $validated['genap_start_date'],
                'end_date' => $validated['genap_end_date'],
                'is_active' => ($validated['semester_active'] === 'genap' && $request->has('is_active')),
                'created_by' => auth()->id()
            ]);

            DB::commit();
            return response()->json(['message' => 'Tahun Pelajaran berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil detail data untuk Edit/Detail Modal.
     */
    public function show($id)
    {
        $data = $this->repository->find($id, ['*'], ['semesters']);
        return response()->json($data);
    }

    /**
     * Memperbarui data Tahun Pelajaran.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|unique:academic_years,name,' . $id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'ganjil_start_date' => 'required|date',
            'ganjil_end_date' => 'required|date|after:ganjil_start_date',
            'genap_start_date' => 'required|date',
            'genap_end_date' => 'required|date|after:genap_start_date',
            'semester_active' => 'required|in:ganjil,genap'
        ]);

        try {
            DB::beginTransaction();

            $academicYear = $this->repository->find($id);
            $academicYear->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'updated_by' => auth()->id()
            ]);

            // Update Semesters
            $academicYear->semesters()->where('type', 'ganjil')->update([
                'start_date' => $validated['ganjil_start_date'],
                'end_date' => $validated['ganjil_end_date'],
                'is_active' => ($validated['semester_active'] === 'ganjil'),
                'updated_by' => auth()->id()
            ]);

            $academicYear->semesters()->where('type', 'genap')->update([
                'start_date' => $validated['genap_start_date'],
                'end_date' => $validated['genap_end_date'],
                'is_active' => ($validated['semester_active'] === 'genap'),
                'updated_by' => auth()->id()
            ]);

            DB::commit();
            return response()->json(['message' => 'Data berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
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
            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }

    /**
     * Mengubah status aktif Tahun Pelajaran secara cepat.
     */
    public function toggleStatus($id)
    {
        try {
            DB::beginTransaction();
            
            // Nonaktifkan semua
            \App\Models\AcademicYear::where('is_active', true)->update(['is_active' => false]);
            \App\Models\Semester::where('is_active', true)->update(['is_active' => false]);

            // Aktifkan yang dipilih
            $academicYear = $this->repository->find($id);
            $academicYear->update(['is_active' => true]);
            
            // Default aktifkan semester ganjil pada tahun tersebut
            $academicYear->semesters()->where('type', 'ganjil')->update(['is_active' => true]);

            DB::commit();
            return response()->json(['message' => 'Status berhasil diubah']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengubah status'], 500);
        }
    }
}
