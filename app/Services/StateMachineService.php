<?php

namespace App\Services;

use App\Models\ApplicantEnrollment;
use App\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\DB;

class StateMachineService
{
    public function __construct(
        protected EnrollmentNumberService $enrollmentNumberService,
        protected InvoiceGeneratorService $invoiceGeneratorService
    ) {}

    /**
     * Transition an enrollment to a new status.
     *
     * @param  ApplicantEnrollment  $enrollment
     * @param  EnrollmentStatus     $to
     * @param  string|null          $reason
     * @return bool
     *
     * @throws \InvalidArgumentException if transition is not allowed.
     */
    public function transition(ApplicantEnrollment $enrollment, EnrollmentStatus $to, ?string $reason = null): bool
    {
        $from = $enrollment->status;

        if (!$this->canTransition($from, $to)) {
            throw new \InvalidArgumentException(
                "Transisi dari [{$from->label()}] ke [{$to->label()}] tidak diizinkan."
            );
        }

        DB::transaction(function () use ($enrollment, $from, $to, $reason) {
            // Generate enrollment number when first registered
            if ($to === EnrollmentStatus::REGISTERED && !$enrollment->enrollment_number) {
                $this->enrollmentNumberService->generate($enrollment);
                // Reload after save inside generate()
                $enrollment->refresh();
            }

            $enrollment->status = $to;

            if ($to === EnrollmentStatus::PERMANENT_STUDENT) {
                $enrollment->enrolled_at = now();
            }

            $enrollment->save();

            $this->logTransition(
                $enrollment,
                $from,
                $to,
                $reason ?? "Status diubah ke " . $to->label()
            );

            // Trigger Invoices
            if ($to === EnrollmentStatus::WAITING_PAYMENT_REG) {
                // Generate Registration Invoice if not yet exists
                if (!\App\Models\Invoice::where('enrollment_id', $enrollment->id)->where('category', 'registration')->exists()) {
                    $this->invoiceGeneratorService->generateRegistrationInvoice($enrollment);
                }
            }

            if ($to === EnrollmentStatus::PASSED) {
                // Generate Re-Registration Invoice
                if (!\App\Models\Invoice::where('enrollment_id', $enrollment->id)->where('category', 're_registration')->exists()) {
                    $this->invoiceGeneratorService->generateReRegistrationInvoice($enrollment);
                }
            }
        });

        return true;
    }

    /**
     * Get allowed next transitions based on current status.
     */
    public function getAllowedTransitions(ApplicantEnrollment $enrollment): array
    {
        return match($enrollment->status) {
            EnrollmentStatus::DRAFT               => [EnrollmentStatus::REGISTERED],
            EnrollmentStatus::REGISTERED          => [EnrollmentStatus::WAITING_PAYMENT_REG, EnrollmentStatus::VERIFIED_REG, EnrollmentStatus::DRAFT],
            EnrollmentStatus::WAITING_PAYMENT_REG => [EnrollmentStatus::VERIFIED_REG, EnrollmentStatus::REJECTED, EnrollmentStatus::DRAFT],
            EnrollmentStatus::VERIFIED_REG        => [EnrollmentStatus::IN_REVIEW, EnrollmentStatus::REJECTED, EnrollmentStatus::DRAFT],
            EnrollmentStatus::IN_REVIEW           => [EnrollmentStatus::PASSED, EnrollmentStatus::WAITING_LIST, EnrollmentStatus::REJECTED],
            EnrollmentStatus::PASSED              => [EnrollmentStatus::WAITING_PAYMENT_FINAL],
            EnrollmentStatus::WAITING_LIST        => [EnrollmentStatus::PASSED, EnrollmentStatus::REJECTED],
            EnrollmentStatus::WAITING_PAYMENT_FINAL => [EnrollmentStatus::SETTLED, EnrollmentStatus::REJECTED],
            EnrollmentStatus::SETTLED             => [EnrollmentStatus::PERMANENT_STUDENT],
            default                               => [],
        };
    }

    /**
     * Check whether a specific transition is permitted.
     */
    public function canTransition(EnrollmentStatus $from, EnrollmentStatus $to): bool
    {
        if ($from === $to) {
            return false;
        }

        // Build a temporary enrollment stub to resolve allowed transitions
        $allowed = match($from) {
            EnrollmentStatus::DRAFT               => [EnrollmentStatus::REGISTERED],
            EnrollmentStatus::REGISTERED          => [EnrollmentStatus::WAITING_PAYMENT_REG, EnrollmentStatus::VERIFIED_REG, EnrollmentStatus::DRAFT],
            EnrollmentStatus::WAITING_PAYMENT_REG => [EnrollmentStatus::VERIFIED_REG, EnrollmentStatus::REJECTED, EnrollmentStatus::DRAFT],
            EnrollmentStatus::VERIFIED_REG        => [EnrollmentStatus::IN_REVIEW, EnrollmentStatus::REJECTED, EnrollmentStatus::DRAFT],
            EnrollmentStatus::IN_REVIEW           => [EnrollmentStatus::PASSED, EnrollmentStatus::WAITING_LIST, EnrollmentStatus::REJECTED],
            EnrollmentStatus::PASSED              => [EnrollmentStatus::WAITING_PAYMENT_FINAL],
            EnrollmentStatus::WAITING_LIST        => [EnrollmentStatus::PASSED, EnrollmentStatus::REJECTED],
            EnrollmentStatus::WAITING_PAYMENT_FINAL => [EnrollmentStatus::SETTLED, EnrollmentStatus::REJECTED],
            EnrollmentStatus::SETTLED             => [EnrollmentStatus::PERMANENT_STUDENT],
            default                               => [],
        };

        return in_array($to, $allowed, strict: true);
    }

    /**
     * Log status transition to database.
     */
    private function logTransition(
        ApplicantEnrollment $enrollment,
        EnrollmentStatus    $from,
        EnrollmentStatus    $to,
        string              $reason
    ): void {
        DB::table('status_logs')->insert([
            'enrollment_id' => $enrollment->id,
            'changed_by'    => auth()->id() ?? 1,
            'from_status'   => $from->value,
            'to_status'     => $to->value,
            'reason'        => $reason,
            'changed_at'    => now(),
        ]);
    }
}
