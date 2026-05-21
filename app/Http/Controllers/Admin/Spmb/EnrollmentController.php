<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Spmb\BulkStatusRequest;
use App\Http\Requests\Admin\Spmb\UpdateStatusRequest;
use App\Repositories\Interfaces\AcademicYearRepositoryInterface;
use App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use App\Services\StateMachineService;
use App\Enums\EnrollmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class EnrollmentController extends Controller
{
    public function __construct(
        protected ApplicantEnrollmentRepositoryInterface $enrollmentRepository,
        protected AcademicYearRepositoryInterface $academicYearRepository,
        protected SpmbTrackRepositoryInterface $trackRepository,
        protected StateMachineService $stateMachine
    ) {}

    /**
     * Tampilan utama halaman manajemen pendaftar.
     */
    public function index()
    {
        $academicYears = $this->academicYearRepository->all();
        $tracks = $this->trackRepository->all(['*'], ['trackType']);

        // Get status list for filter dropdown
        $statuses = EnrollmentStatus::cases();

        return view('admin.spmb.pendaftar.index', compact('academicYears', 'tracks', 'statuses'));
    }

    /**
     * Get data pendaftar server-side untuk DataTables.
     */
    public function getData(Request $request)
    {
        $query = $this->enrollmentRepository->query(['applicant', 'spmbTrack.trackType', 'spmbTrack.spmbConfiguration.academicYear']);

        if ($request->filled('academic_year_id')) {
            $query->whereHas('spmbTrack.spmbConfiguration', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->filled('spmb_track_id')) {
            $query->where('spmb_track_id', $request->spmb_track_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check">
                            <input class="form-check-input check-item" type="checkbox" value="' . $row->id . '">
                        </div>';
            })
            ->addColumn('action', function ($row) {
                return '<div class="dropdown">
                            <button class="btn btn-sm btn-light-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item btn-detail" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-eye me-2"></i>Detail</a></li>
                                <li><a class="dropdown-item btn-status" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-arrows-left-right me-2"></i>Ubah Status</a></li>
                            </ul>
                        </div>';
            })
            ->addColumn('enrollment_number', function ($row) {
                return $row->enrollment_number ?: '<span class="text-muted italic">-</span>';
            })
            ->addColumn('full_name', function ($row) {
                return $row->applicant ? $row->applicant->full_name : '-';
            })
            ->addColumn('track_name', function ($row) {
                return $row->spmbTrack && $row->spmbTrack->trackType ? $row->spmbTrack->trackType->name : '-';
            })
            ->addColumn('academic_year', function ($row) {
                return $row->spmbTrack && $row->spmbTrack->spmbConfiguration && $row->spmbTrack->spmbConfiguration->academicYear 
                    ? $row->spmbTrack->spmbConfiguration->academicYear->name 
                    : '-';
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;
                return '<span class="badge ' . $status->badgeClass() . '">' . $status->label() . '</span>';
            })
            ->addColumn('created_at_formatted', function ($row) {
                return $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-';
            })
            ->rawColumns(['checkbox', 'action', 'enrollment_number', 'status_badge'])
            ->make(true);
    }

    /**
     * Mengambil detail pendaftar lengkap untuk modal detail (JSON).
     */
    public function show($id)
    {
        $enrollment = $this->enrollmentRepository->find(
            $id, 
            ['*'], 
            [
                'applicant', 
                'spmbTrack.trackType', 
                'spmbTrack.spmbConfiguration.academicYear', 
                'formData.field', 
                'invoices.items', 
                'invoices.payments.confirmedBy',
                'statusLogs.changer'
            ]
        );

        if (!$enrollment) {
            return response()->json(['message' => 'Pendaftar tidak ditemukan'], 404);
        }

        // Get allowed state transitions for the status modal dropdown
        $allowedTransitions = [];
        foreach (EnrollmentStatus::cases() as $targetStatus) {
            if ($this->stateMachine->canTransition($enrollment->status, $targetStatus)) {
                $allowedTransitions[] = [
                    'value' => $targetStatus->value,
                    'label' => $targetStatus->label(),
                ];
            }
        }

        return response()->json([
            'enrollment' => $enrollment,
            'allowed_transitions' => $allowedTransitions
        ]);
    }

    /**
     * Memperbarui status pendaftar individu.
     */
    public function updateStatus(UpdateStatusRequest $request, $id)
    {
        $enrollment = $this->enrollmentRepository->find($id);

        if (!$enrollment) {
            return response()->json(['message' => 'Pendaftar tidak ditemukan'], 404);
        }

        $targetStatus = EnrollmentStatus::from($request->status);

        try {
            DB::transaction(function () use ($enrollment, $targetStatus, $request) {
                $success = $this->stateMachine->transition(
                    $enrollment, 
                    $targetStatus, 
                    $request->reason ?: 'Diubah manual oleh Admin'
                );

                if (!$success) {
                    throw new \Exception("Transisi status dari [{$enrollment->status->label()}] ke [{$targetStatus->label()}] tidak diizinkan.");
                }
            });

            return response()->json(['message' => 'Status pendaftaran berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui status pendaftar secara massal.
     */
    public function bulkStatus(BulkStatusRequest $request)
    {
        $targetStatus = EnrollmentStatus::from($request->status);
        $reason = $request->reason ?: 'Diubah massal oleh Admin';

        try {
            DB::transaction(function () use ($request, $targetStatus, $reason) {
                foreach ($request->ids as $id) {
                    $enrollment = $this->enrollmentRepository->find($id);
                    if ($enrollment) {
                        $success = $this->stateMachine->transition($enrollment, $targetStatus, $reason);
                        if (!$success) {
                            throw new \Exception("Pendaftar #{$enrollment->enrollment_number} tidak dapat diubah ke status [{$targetStatus->label()}].");
                        }
                    }
                }
            });

            return response()->json(['message' => 'Status massal pendaftar berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memproses status massal: ' . $e->getMessage()], 500);
        }
    }
}
