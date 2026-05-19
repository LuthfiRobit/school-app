<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Spmb\StoreSpmbTrackRequest;
use App\Http\Requests\Admin\Spmb\UpdateSpmbTrackRequest;
use App\Repositories\Interfaces\SpmbConfigurationRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackAssessmentRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackFeeRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackFormFieldRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SpmbTrackController extends Controller
{
    public function __construct(
        protected SpmbTrackRepositoryInterface $spmbTrackRepository,
        protected SpmbConfigurationRepositoryInterface $spmbConfigurationRepository,
        protected SpmbTrackFeeRepositoryInterface $spmbTrackFeeRepository,
        protected SpmbTrackAssessmentRepositoryInterface $spmbTrackAssessmentRepository,
        protected SpmbTrackFormFieldRepositoryInterface $spmbTrackFormFieldRepository
    ) {
    }

    /**
     * Tampilan utama halaman Manajemen Jalur SPMB per Konfigurasi
     */
    public function index($configurationId)
    {
        $spmbConfig = $this->spmbConfigurationRepository->find($configurationId, ['*'], ['academicYear']);
        $masterTracks = \App\Models\MasterTrackType::where('is_active', true)->get();
        return view('admin.spmb.configurations.tracks.index', compact('spmbConfig', 'masterTracks'));
    }

    /**
     * DataTables for Tracks under a specific Configuration
     */
    public function data($configurationId)
    {
        $tracks = $this->spmbTrackRepository->all(
            ['*'],
            ['trackType', 'creator']
        )->where('spmb_configuration_id', $configurationId);

        return DataTables::of($tracks)
            ->addIndexColumn()
            ->addColumn('track_name', function ($row) {
                return $row->trackType ? $row->trackType->name : '-';
            })
            ->editColumn('registration_fee', function ($row) {
                return 'Rp ' . number_format($row->registration_fee, 0, ',', '.');
            })
            ->editColumn('status', function ($row) {
                $statusClass = [
                    'active' => 'success',
                    'closed' => 'danger',
                    'full' => 'warning'
                ][$row->status] ?? 'secondary';
                return '<span class="badge bg-' . $statusClass . '">' . strtoupper($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown">
                            <button class="btn btn-sm btn-light-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="editTrack(' . $row->id . ')"><i class="ti ti-edit me-2"></i>Edit Jalur</a></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="manageMappings(' . $row->id . ')"><i class="ti ti-settings me-2"></i>Kelola Syarat</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteTrack(' . $row->id . ')"><i class="ti ti-trash me-2"></i>Hapus</a></li>
                            </ul>
                        </div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created track in storage.
     */
    public function store(StoreSpmbTrackRequest $request, $configurationId)
    {
        try {
            $data = $request->validated();
            $data['spmb_configuration_id'] = $configurationId;

            $this->spmbTrackRepository->create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Jalur berhasil ditambahkan ke Tahun Ajaran ini.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan jalur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified track.
     */
    public function show($configurationId, $id)
    {
        $track = $this->spmbTrackRepository->find($id, ['*'], ['trackType', 'fees', 'assessments', 'formFields']);
        return response()->json($track);
    }

    /**
     * Update the specified track in storage.
     */
    public function update(UpdateSpmbTrackRequest $request, $configurationId, $id)
    {
        try {
            $this->spmbTrackRepository->update($id, $request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Konfigurasi jalur berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui jalur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified track from storage.
     */
    public function destroy($configurationId, $id)
    {
        try {
            $this->spmbTrackRepository->delete($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Jalur berhasil dihapus dari Tahun Ajaran ini.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus jalur.'
            ], 500);
        }
    }

    /**
     * Sync mappings for the track (Fees, Assessments, Form Fields)
     */
    public function syncMappings(Request $request, $configurationId, $id)
    {
        // Not implemented full logic here yet. Will be expanded based on frontend structure.
        // It could delete existing and recreate, or update existing.
        // For simplicity, we assume an array of fees, assessments, and form_fields is sent.
        return response()->json([
            'status' => 'success',
            'message' => 'Mappings saved successfully (Mock).'
        ]);
    }
}
