<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Spmb\BulkDecisionRequest;
use App\Http\Requests\Admin\Spmb\SetDecisionRequest;
use App\Http\Requests\Admin\Spmb\UpsertAssessmentRequest;
use App\Models\Assessment;
use App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface;
use App\Repositories\Interfaces\AssessmentRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use App\Services\AssessmentScoreService;
use App\Services\StateMachineService;
use App\Enums\EnrollmentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AssessmentController extends Controller
{
    public function __construct(
        protected ApplicantEnrollmentRepositoryInterface $enrollmentRepository,
        protected AssessmentRepositoryInterface          $assessmentRepository,
        protected SpmbTrackRepositoryInterface           $trackRepository,
        protected StateMachineService                    $stateMachine,
        protected AssessmentScoreService                 $scoreService
    ) {}

    /**
     * Tampilan utama halaman manajemen penilaian.
     * Menampilkan daftar pendaftar yang siap dinilai (status: verified_reg, in_review).
     */
    public function index()
    {
        $tracks = $this->trackRepository->all(['*'], ['trackType', 'spmbConfiguration.academicYear']);

        // Valid assessment statuses are those eligible for review/decision
        $statuses = collect([
            EnrollmentStatus::VERIFIED_REG,
            EnrollmentStatus::IN_REVIEW,
            EnrollmentStatus::PASSED,
            EnrollmentStatus::WAITING_LIST,
            EnrollmentStatus::REJECTED,
        ])->map(fn($s) => ['value' => $s->value, 'label' => $s->label()]);

        return view('admin.spmb.penilaian.index', compact('tracks', 'statuses'));
    }

    /**
     * Get data pendaftar server-side untuk DataTables modul penilaian.
     */
    public function getData(Request $request)
    {
        $query = $this->enrollmentRepository->query([
            'applicant',
            'spmbTrack.trackType',
            'spmbTrack.spmbConfiguration.academicYear',
            'assessments',
        ]);

        // Default filter: only show enrollments in assessable states
        $assessableStatuses = [
            EnrollmentStatus::VERIFIED_REG->value,
            EnrollmentStatus::IN_REVIEW->value,
            EnrollmentStatus::PASSED->value,
            EnrollmentStatus::WAITING_LIST->value,
            EnrollmentStatus::REJECTED->value,
        ];

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', $assessableStatuses);
        }

        if ($request->filled('spmb_track_id')) {
            $query->where('spmb_track_id', $request->spmb_track_id);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check">
                            <input class="form-check-input check-item" type="checkbox" value="' . $row->id . '">
                        </div>';
            })
            ->addColumn('action', function ($row) {
                $detailBtn = '<a class="dropdown-item btn-detail" href="javascript:void(0)" data-id="' . $row->id . '">
                                  <i class="ti ti-clipboard-list me-2"></i>Input / Lihat Nilai
                              </a>';
                              
                $allAssessed = $this->scoreService->isAllComponentsAssessed($row);
                
                $decisionBtn = '';
                if ($allAssessed) {
                    $decisionBtn = '<li><a class="dropdown-item btn-decision" href="javascript:void(0)" data-id="' . $row->id . '">
                                        <i class="ti ti-award me-2"></i>Tetapkan Kelulusan
                                    </a></li>';
                }

                return '<div class="dropdown">
                            <button class="btn btn-sm btn-light-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>' . $detailBtn . '</li>
                                ' . $decisionBtn . '
                            </ul>
                        </div>';
            })
            ->addColumn('enrollment_number', function ($row) {
                return $row->enrollment_number ?: '<span class="text-muted fst-italic">-</span>';
            })
            ->addColumn('full_name', function ($row) {
                return $row->applicant ? $row->applicant->full_name : '-';
            })
            ->addColumn('track_name', function ($row) {
                return $row->spmbTrack && $row->spmbTrack->trackType
                    ? $row->spmbTrack->trackType->name
                    : '-';
            })
            ->addColumn('academic_year', function ($row) {
                return $row->spmbTrack && $row->spmbTrack->spmbConfiguration && $row->spmbTrack->spmbConfiguration->academicYear
                    ? $row->spmbTrack->spmbConfiguration->academicYear->name
                    : '-';
            })
            ->addColumn('assessment_progress', function ($row) {
                // Count total components vs. assessed
                $totalComponents = DB::table('spmb_track_assessments')
                    ->where('spmb_track_id', $row->spmb_track_id)
                    ->count();

                $assessedCount = $row->assessments
                    ->whereNotNull('score')
                    ->count();

                if ($totalComponents === 0) {
                    return '<span class="text-muted fst-italic">Tidak ada komponen</span>';
                }

                $percentage = round(($assessedCount / $totalComponents) * 100);
                $badgeClass = $percentage >= 100 ? 'bg-success' : ($percentage > 0 ? 'bg-warning' : 'bg-secondary');

                return '<div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar ' . $badgeClass . '" style="width: ' . $percentage . '%"></div>
                            </div>
                            <small class="text-nowrap">' . $assessedCount . '/' . $totalComponents . '</small>
                        </div>';
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;
                return '<span class="badge ' . $status->badgeClass() . '">' . $status->label() . '</span>';
            })
            ->addColumn('score_formatted', function ($row) {
                $score = $this->scoreService->calculateWeightedScore($row);
                return '<strong>' . number_format($score, 2) . '</strong>';
            })
            ->addColumn('created_at_formatted', function ($row) {
                return $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-';
            })
            ->rawColumns(['checkbox', 'action', 'enrollment_number', 'assessment_progress', 'status_badge', 'score_formatted'])
            ->make(true);
    }

    /**
     * Detail nilai per komponen untuk satu pendaftar (JSON untuk modal inline edit).
     */
    public function show($enrollmentId)
    {
        $enrollment = $this->enrollmentRepository->find(
            $enrollmentId,
            ['*'],
            [
                'applicant',
                'spmbTrack.trackType',
                'spmbTrack.spmbConfiguration.academicYear',
                'assessments.trackAssessment.assessmentType',
                'assessments.assessor',
            ]
        );

        if (!$enrollment) {
            return response()->json(['message' => 'Pendaftar tidak ditemukan'], 404);
        }

        // Merge track assessment components with existing scores
        $trackAssessments = DB::table('spmb_track_assessments')
            ->join('master_assessment_types', 'spmb_track_assessments.master_assessment_type_id', '=', 'master_assessment_types.id')
            ->where('spmb_track_assessments.spmb_track_id', $enrollment->spmb_track_id)
            ->orderBy('spmb_track_assessments.display_order')
            ->select([
                'spmb_track_assessments.id as mapping_id',
                'spmb_track_assessments.weight',
                'spmb_track_assessments.passing_score',
                'spmb_track_assessments.display_order',
                'master_assessment_types.id as assessment_type_id',
                'master_assessment_types.name as assessment_name',
                'master_assessment_types.input_type as type',
            ])
            ->get();

        // Map existing scores to each component
        $existingScores = $enrollment->assessments->keyBy('spmb_track_assessment_id');

        $components = $trackAssessments->map(function ($component) use ($existingScores) {
            $existing = $existingScores->get($component->mapping_id);
            return [
                'mapping_id'        => $component->mapping_id,
                'assessment_name'   => $component->assessment_name,
                'type'              => $component->type,
                'weight'            => (float) $component->weight,
                'passing_score'     => (float) $component->passing_score,
                'display_order'     => $component->display_order,
                'score'             => $existing ? (float) $existing->score : null,
                'grade'             => $existing ? $existing->grade : null,
                'notes'             => $existing ? $existing->notes : null,
                'assessed_at'       => $existing && $existing->assessed_at
                    ? $existing->assessed_at->format('d-m-Y H:i')
                    : null,
                'assessor_name'     => $existing && $existing->assessor
                    ? $existing->assessor->name
                    : null,
            ];
        });

        // Calculate weighted score
        $weightedScore = $this->scoreService->calculateWeightedScore($enrollment);
        $allAssessed   = $this->scoreService->isAllComponentsAssessed($enrollment);

        // Get waitlist statistics for the same track
        $waitlist = DB::table('applicant_enrollments')
            ->join('applicants', 'applicant_enrollments.applicant_id', '=', 'applicants.id')
            ->where('applicant_enrollments.spmb_track_id', $enrollment->spmb_track_id)
            ->where('applicant_enrollments.status', EnrollmentStatus::WAITING_LIST->value)
            ->whereNull('applicant_enrollments.deleted_at')
            ->orderBy('applicant_enrollments.waitlist_order')
            ->select([
                'applicant_enrollments.id',
                'applicant_enrollments.waitlist_order',
                'applicants.full_name as applicant_name',
                'applicant_enrollments.enrollment_number'
            ])
            ->get();

        $maxWaitlistOrder = $waitlist->max('waitlist_order') ?? 0;

        return response()->json([
            'enrollment'     => [
                'id'               => $enrollment->id,
                'enrollment_number' => $enrollment->enrollment_number,
                'status'           => $enrollment->status->value,
                'status_label'     => $enrollment->status->label(),
                'status_badge'     => $enrollment->status->badgeClass(),
                'waitlist_order'   => $enrollment->waitlist_order,
            ],
            'applicant'      => [
                'full_name' => $enrollment->applicant?->full_name,
                'nik'       => $enrollment->applicant?->nik ?? null,
                'nisn'      => $enrollment->applicant?->nisn ?? null,
            ],
            'track'          => [
                'name'         => $enrollment->spmbTrack?->trackType?->name,
                'academic_year' => $enrollment->spmbTrack?->spmbConfiguration?->academicYear?->name,
            ],
            'components'     => $components,
            'weighted_score' => round($weightedScore, 2),
            'all_assessed'   => $allAssessed,
            'waitlist_info'  => [
                'current_count'  => $waitlist->count(),
                'max_order'      => $maxWaitlistOrder,
                'next_suggested' => $maxWaitlistOrder > 0 ? $maxWaitlistOrder + 1 : 1,
                'list'           => $waitlist->map(fn($w) => [
                    'id'                => $w->id,
                    'applicant_name'    => $w->applicant_name,
                    'waitlist_order'    => $w->waitlist_order,
                    'enrollment_number' => $w->enrollment_number,
                ])
            ]
        ]);
    }

    /**
     * Simpan / update nilai satu komponen penilaian secara inline (upsert).
     *
     * Logika: Assessment::updateOrCreate berdasarkan (enrollment_id, spmb_track_assessment_id).
     */
    public function upsert(UpsertAssessmentRequest $request, $enrollmentId)
    {
        $enrollment = $this->enrollmentRepository->find($enrollmentId);

        if (!$enrollment) {
            return response()->json(['message' => 'Pendaftar tidak ditemukan'], 404);
        }

        // Verify the spmb_track_assessment_id belongs to the enrollment's track and get its metadata
        $componentMapping = DB::table('spmb_track_assessments')
            ->join('master_assessment_types', 'spmb_track_assessments.master_assessment_type_id', '=', 'master_assessment_types.id')
            ->where('spmb_track_assessments.id', $request->spmb_track_assessment_id)
            ->where('spmb_track_assessments.spmb_track_id', $enrollment->spmb_track_id)
            ->select([
                'spmb_track_assessments.id',
                'spmb_track_assessments.passing_score',
                'master_assessment_types.input_type'
            ])
            ->first();

        if (!$componentMapping) {
            return response()->json(['message' => 'Komponen penilaian tidak valid untuk jalur ini'], 422);
        }

        $score = $request->score;
        $grade = $request->grade;

        // Auto-calculate / sync score and grade depending on component input type
        if ($componentMapping->input_type === 'pass_fail') {
            if ($grade === 'pass') {
                $score = 100.0;
            } elseif ($grade === 'fail') {
                $score = 0.0;
            } else {
                $score = null;
            }
        } else {
            // input_type = 'score'
            if ($score !== null) {
                $passingScore = (float) $componentMapping->passing_score;
                if ($passingScore > 0) {
                    $grade = $score >= $passingScore ? 'pass' : 'fail';
                } else {
                    $grade = 'pass'; // default to pass if no passing score is set
                }
            } else {
                $grade = null;
            }
        }

        try {
            $assessment = Assessment::updateOrCreate(
                [
                    'enrollment_id'             => $enrollment->id,
                    'spmb_track_assessment_id'  => $request->spmb_track_assessment_id,
                ],
                [
                    'score'       => $score,
                    'grade'       => $grade,
                    'notes'       => $request->notes,
                    'assessed_by' => auth()->id(),
                    'assessed_at' => now(),
                ]
            );

            // Recalculate weighted score after upsert
            $weightedScore = $this->scoreService->calculateWeightedScore($enrollment);
            $allAssessed   = $this->scoreService->isAllComponentsAssessed($enrollment);

            return response()->json([
                'message'       => 'Nilai berhasil disimpan',
                'assessment'    => [
                    'id'          => $assessment->id,
                    'score'       => $assessment->score,
                    'grade'       => $assessment->grade,
                    'notes'       => $assessment->notes,
                    'assessed_at' => $assessment->assessed_at?->format('d-m-Y H:i'),
                ],
                'weighted_score' => round($weightedScore, 2),
                'all_assessed'   => $allAssessed,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan nilai: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Tetapkan status kelulusan untuk satu pendaftar.
     *
     * Alur (dalam DB Transaction):
     * 1. Validasi: enrollment harus verified_reg atau in_review
     * 2. Jika belum in_review → transition ke in_review terlebih dahulu
     * 3. Transition ke target status via StateMachineService
     * 4. Jika WAITING_LIST: set waitlist_order
     */
    public function setDecision(SetDecisionRequest $request, $enrollmentId)
    {
        $enrollment = $this->enrollmentRepository->find($enrollmentId);

        if (!$enrollment) {
            return response()->json(['message' => 'Pendaftar tidak ditemukan'], 404);
        }

        // Validate: must be in an assessable state
        $allowedStates = [EnrollmentStatus::VERIFIED_REG, EnrollmentStatus::IN_REVIEW, EnrollmentStatus::WAITING_LIST];
        if (!in_array($enrollment->status, $allowedStates, true)) {
            return response()->json([
                'message' => 'Penetapan kelulusan hanya dapat dilakukan pada pendaftar dengan status: Pembayaran Terverifikasi, Sedang Dinilai, atau Cadangan.'
            ], 422);
        }

        $targetStatus = EnrollmentStatus::from($request->status);
        $reason       = $request->reason ?: 'Ditetapkan oleh Admin';

        try {
            DB::transaction(function () use ($enrollment, $targetStatus, $request, $reason) {
                // Step 1: If still in verified_reg, move to in_review first
                if ($enrollment->status === EnrollmentStatus::VERIFIED_REG) {
                    $this->stateMachine->transition(
                        $enrollment,
                        EnrollmentStatus::IN_REVIEW,
                        'Proses penilaian dimulai oleh Admin'
                    );
                    $enrollment->refresh();
                }

                // Step 2: Transition to the final decision status
                $this->stateMachine->transition($enrollment, $targetStatus, $reason);
                $enrollment->refresh();

                // Step 3: Handle WAITING_LIST order
                if ($targetStatus === EnrollmentStatus::WAITING_LIST && $request->filled('waitlist_order')) {
                    $enrollment->waitlist_order = (int) $request->waitlist_order;
                    $enrollment->save();
                }

                // Step 4: Optionally clear waitlist_order if decision is changed away from waiting list
                if ($targetStatus !== EnrollmentStatus::WAITING_LIST) {
                    $enrollment->waitlist_order = null;
                    $enrollment->save();
                }
            });

            return response()->json([
                'message' => 'Status kelulusan berhasil ditetapkan: ' . $targetStatus->label(),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menetapkan kelulusan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Tetapkan status kelulusan secara massal untuk beberapa pendaftar.
     *
     * Semua ID yang gagal akan di-skip, error akan dilaporkan di akhir.
     */
    public function bulkDecision(BulkDecisionRequest $request)
    {
        $targetStatus = EnrollmentStatus::from($request->status);
        $reason       = $request->reason ?: 'Ditetapkan massal oleh Admin';
        $errors       = [];
        $successCount = 0;

        try {
            DB::transaction(function () use ($request, $targetStatus, $reason, &$errors, &$successCount) {
                foreach ($request->ids as $id) {
                    $enrollment = $this->enrollmentRepository->find($id);

                    if (!$enrollment) {
                        $errors[] = "Pendaftar ID #{$id} tidak ditemukan.";
                        continue;
                    }

                    $allowedStates = [
                        EnrollmentStatus::VERIFIED_REG,
                        EnrollmentStatus::IN_REVIEW,
                        EnrollmentStatus::WAITING_LIST,
                    ];

                    if (!in_array($enrollment->status, $allowedStates, true)) {
                        $errors[] = "{$enrollment->applicant?->full_name} [{$enrollment->enrollment_number}]: Status [{$enrollment->status->label()}] tidak dapat diubah ke [{$targetStatus->label()}].";
                        continue;
                    }

                    try {
                        // Move to in_review first if needed
                        if ($enrollment->status === EnrollmentStatus::VERIFIED_REG) {
                            $this->stateMachine->transition(
                                $enrollment,
                                EnrollmentStatus::IN_REVIEW,
                                'Proses penilaian massal dimulai oleh Admin'
                            );
                            $enrollment->refresh();
                        }

                        $this->stateMachine->transition($enrollment, $targetStatus, $reason);
                        $enrollment->refresh();

                        // Clear waitlist_order if changed away from waiting_list
                        if ($targetStatus !== EnrollmentStatus::WAITING_LIST) {
                            $enrollment->waitlist_order = null;
                            $enrollment->save();
                        }

                        $successCount++;
                    } catch (\InvalidArgumentException $e) {
                        $errors[] = "{$enrollment->applicant?->full_name} [{$enrollment->enrollment_number}]: {$e->getMessage()}";
                    }
                }

                // Rollback the entire transaction if ALL failed
                if ($successCount === 0 && count($errors) > 0) {
                    throw new \Exception('Semua pendaftar gagal diproses.');
                }
            });
        } catch (\Exception $e) {
            if ($successCount === 0) {
                return response()->json([
                    'message' => 'Gagal memproses kelulusan massal.',
                    'errors'  => $errors,
                ], 500);
            }
        }

        $message = "{$successCount} pendaftar berhasil ditetapkan sebagai [{$targetStatus->label()}].";
        if (!empty($errors)) {
            $message .= ' Beberapa pendaftar dilewati karena kesalahan.';
        }

        return response()->json([
            'message'       => $message,
            'success_count' => $successCount,
            'errors'        => $errors,
        ]);
    }
}
