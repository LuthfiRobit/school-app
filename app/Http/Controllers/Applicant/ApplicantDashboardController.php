<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SpmbConfigurationRepositoryInterface;
use App\Repositories\Interfaces\SchoolIdentityRepositoryInterface;
use App\Repositories\Interfaces\BankAccountRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ApplicantDashboardController extends Controller
{
    public function __construct(
        protected SpmbConfigurationRepositoryInterface $spmbConfigRepo,
        protected SchoolIdentityRepositoryInterface    $schoolIdentityRepo,
        protected BankAccountRepositoryInterface       $bankAccountRepo,
        protected SpmbTrackRepositoryInterface         $spmbTrackRepo
    ) {}

    /**
     * Tampilkan halaman dashboard utama calon siswa.
     */
    public function index(): View
    {
        $user = Auth::user();
        $applicant = $user->applicant;
        $activeConfig = $this->spmbConfigRepo->getActiveConfiguration();
        $schoolIdentity = $this->schoolIdentityRepo->first();

        $enrollment = null;
        $statusLogs = collect();

        if ($activeConfig && $applicant) {
            $enrollment = $applicant->enrollments()
                ->whereHas('spmbTrack', function ($query) use ($activeConfig) {
                    $query->where('spmb_configuration_id', $activeConfig->id);
                })
                ->orderBy('created_at', 'desc')
                ->first();

            if ($enrollment) {
                // Eager load relationships
                $enrollment->load([
                    'spmbTrack.trackType',
                    'invoices.payments' => function ($q) {
                        $q->orderBy('created_at', 'desc');
                    },
                    'assessments.trackAssessment.assessmentType'
                ]);

                // Fetch status logs for this enrollment
                $statusLogs = $enrollment->statusLogs()
                    ->orderBy('changed_at', 'desc')
                    ->take(5)
                    ->get();
            }
        }

        // Determine active step for stepper
        $activeStep = 1;
        $statusKey = 'no_enrollment';
        $announcementVisible = true;
        $availableTracksForReapply = collect();

        if ($enrollment) {
            $realStatusKey = $enrollment->status->value;
            $statusKey = $realStatusKey;

            // Logika Pengumuman (TPD BL-VAL-03 & FR-26)
            if (in_array($realStatusKey, ['in_review', 'waiting_list', 'passed', 'rejected'])) {
                if (!$enrollment->announcement_visible_at || $enrollment->announcement_visible_at->isFuture()) {
                    $announcementVisible = false;
                    
                    // Mask status as 'in_review' if not yet announced
                    if (in_array($realStatusKey, ['waiting_list', 'passed', 'rejected'])) {
                        $statusKey = 'in_review';
                    }
                }
            }

            // Fetch alternative tracks for reapply if rejected and announcement is visible
            if ($realStatusKey === 'rejected' && $announcementVisible) {
                $usedTrackIds = $applicant->enrollments()->pluck('spmb_track_id')->toArray();
                $availableTracksForReapply = $this->spmbTrackRepo->getActiveTracksWithQuota()
                    ->filter(function ($track) use ($usedTrackIds) {
                        return !in_array($track->id, $usedTrackIds);
                    })
                    ->values();
            }

            $activeStep = match ($statusKey) {
                'draft' => 1,
                'waiting_payment_reg', 'registered' => 2,
                'verified_reg', 'in_review', 'waiting_list' => 3,
                'passed', 'rejected' => 4,
                'waiting_payment_final', 'settled' => 5,
                'permanent_student' => 6,
                default => 1,
            };
        }

        $asalSekolah = 'Belum Mengisi Formulir';
        if ($enrollment) {
            $sekolahField = $enrollment->formData()
                ->whereHas('field', function($q) {
                    $q->where('field_name', 'like', '%sekolah%')
                      ->orWhere('field_label', 'like', '%sekolah%');
                })
                ->first();
            if ($sekolahField) {
                $asalSekolah = $sekolahField->value;
            }
        }

        $bankAccounts = $this->bankAccountRepo->query()->where('is_active', 1)->get();

        return view('portal.dashboard', compact(
            'user',
            'applicant',
            'activeConfig',
            'schoolIdentity',
            'enrollment',
            'statusLogs',
            'activeStep',
            'statusKey',
            'bankAccounts',
            'asalSekolah',
            'announcementVisible',
            'availableTracksForReapply'
        ));
    }
}
