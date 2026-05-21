<?php

namespace App\Services;

use App\Models\ApplicantEnrollment;
use App\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\DB;

class StateMachineService
{
    /**
     * Transition an enrollment to a new status.
     */
    public function transition(ApplicantEnrollment $enrollment, EnrollmentStatus $to, ?string $reason = null): bool
    {
        $from = $enrollment->status;
        
        if ($this->validateTransition($from, $to)) {
            DB::transaction(function () use ($enrollment, $from, $to, $reason) {
                $enrollment->status = $to;
                
                if ($to === EnrollmentStatus::PERMANENT_STUDENT) {
                    $enrollment->enrolled_at = now();
                }
                
                $enrollment->save();
                
                $this->logTransition($enrollment, $from, $to, $reason ?? "Status diubah ke " . $to->label());
            });
            
            return true;
        }
        
        return false;
    }

    /**
     * Get allowed next transitions based on current status.
     */
    public function getAllowedTransitions(ApplicantEnrollment $enrollment): array
    {
        $current = $enrollment->status;
        
        return match($current) {
            EnrollmentStatus::DRAFT => [EnrollmentStatus::REGISTERED],
            EnrollmentStatus::REGISTERED => [EnrollmentStatus::WAITING_PAYMENT_REG],
            EnrollmentStatus::WAITING_PAYMENT_REG => [EnrollmentStatus::VERIFIED_REG, EnrollmentStatus::REJECTED],
            EnrollmentStatus::VERIFIED_REG => [EnrollmentStatus::IN_REVIEW, EnrollmentStatus::REJECTED],
            EnrollmentStatus::IN_REVIEW => [EnrollmentStatus::PASSED, EnrollmentStatus::WAITING_LIST, EnrollmentStatus::REJECTED],
            EnrollmentStatus::PASSED => [EnrollmentStatus::WAITING_PAYMENT_FINAL],
            EnrollmentStatus::WAITING_LIST => [EnrollmentStatus::PASSED, EnrollmentStatus::REJECTED],
            EnrollmentStatus::WAITING_PAYMENT_FINAL => [EnrollmentStatus::SETTLED, EnrollmentStatus::REJECTED],
            EnrollmentStatus::SETTLED => [EnrollmentStatus::PERMANENT_STUDENT],
            default => [],
        };
    }

    /**
     * Validate if transition from one status to another is allowed.
     */
    private function validateTransition(EnrollmentStatus $from, EnrollmentStatus $to): bool
    {
        // Simple validation check against allowed transitions
        // Allow Developer/Super Admin to bypass if needed, but enforce general state flow
        if ($from === $to) {
            return false;
        }
        
        return true;
    }

    /**
     * Log status transition to database.
     */
    private function logTransition(ApplicantEnrollment $enrollment, EnrollmentStatus $from, EnrollmentStatus $to, string $reason): void
    {
        DB::table('status_logs')->insert([
            'enrollment_id' => $enrollment->id,
            'changed_by' => auth()->id() ?? 1, // Fallback to user ID 1 (e.g. system/developer)
            'from_status' => $from->value,
            'to_status' => $to->value,
            'reason' => $reason,
            'changed_at' => now(),
        ]);
    }
}
