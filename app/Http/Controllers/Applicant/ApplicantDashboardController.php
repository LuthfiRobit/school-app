<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\SpmbConfigurationRepositoryInterface;
use App\Repositories\Interfaces\SchoolIdentityRepositoryInterface;
use App\Repositories\Interfaces\BankAccountRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ApplicantDashboardController extends Controller
{
    public function __construct(
        protected SpmbConfigurationRepositoryInterface $spmbConfigRepo,
        protected SchoolIdentityRepositoryInterface $schoolIdentityRepo,
        protected BankAccountRepositoryInterface $bankAccountRepo
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
                    'assessments.assessmentType'
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

        if ($enrollment) {
            $statusKey = $enrollment->status->value;
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
            'asalSekolah'
        ));
    }
}
