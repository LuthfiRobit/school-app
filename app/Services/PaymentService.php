<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function __construct(
        protected StateMachineService $stateMachine
    ) {}

    // =========================================================================
    //  UPLOAD FLOW: Pendaftar upload bukti transfer
    // =========================================================================

    /**
     * Record a payment proof uploaded by an applicant.
     * Status: pending (awaiting admin verification)
     *
     * @param  Invoice       $invoice
     * @param  UploadedFile  $proofFile  The uploaded proof image/pdf
     * @param  float         $amount     The amount claimed by the applicant
     * @return Payment
     *
     * @throws \InvalidArgumentException if invoice is already paid or cancelled.
     */
    public function submitProof(Invoice $invoice, UploadedFile $proofFile, float $amount): Payment
    {
        $this->ensureInvoiceIsPayable($invoice);

        $path = $proofFile->store("proofs/{$invoice->id}", 'public');

        return DB::transaction(function () use ($invoice, $path, $amount) {
            $payment = Payment::create([
                'invoice_id'         => $invoice->id,
                'confirmed_by'       => null,
                'amount'             => $amount,
                'confirmed_amount'   => null,
                'payment_proof_path' => $path,
                'status'             => PaymentStatus::PENDING,
                'rejection_reason'   => null,
                'input_method'       => 'upload',
                'confirmed_at'       => null,
            ]);

            return $payment;
        });
    }

    // =========================================================================
    //  VERIFICATION FLOW: Admin confirm or reject an uploaded proof
    // =========================================================================

    /**
     * Admin confirms a pending uploaded payment.
     * The confirmed_amount may differ from the claimed amount (partial or full).
     *
     * After confirmation, the invoice paid_amount and status are recalculated.
     * If the invoice reaches `paid`, the enrollment advances automatically.
     *
     * @param  Payment    $payment          The pending payment to confirm.
     * @param  float      $confirmedAmount  The actual verified amount (may differ from claimed).
     * @return Payment
     *
     * @throws \InvalidArgumentException if payment is not in pending status.
     */
    public function confirmProof(Payment $payment, float $confirmedAmount): Payment
    {
        if ($payment->status !== PaymentStatus::PENDING) {
            throw new \InvalidArgumentException(
                "Hanya pembayaran berstatus [pending] yang dapat dikonfirmasi."
            );
        }

        DB::transaction(function () use ($payment, $confirmedAmount) {
            $payment->update([
                'status'           => PaymentStatus::CONFIRMED,
                'confirmed_amount' => $confirmedAmount,
                'confirmed_by'     => auth()->id(),
                'confirmed_at'     => now(),
            ]);

            $this->recalculateInvoice($payment->invoice);
        });

        return $payment->refresh();
    }

    /**
     * Admin rejects a pending uploaded payment with a reason.
     * The enrollment stays in its current status — the applicant can upload again.
     *
     * @param  Payment  $payment
     * @param  string   $reason
     * @return Payment
     *
     * @throws \InvalidArgumentException if payment is not pending.
     */
    public function rejectProof(Payment $payment, string $reason): Payment
    {
        if ($payment->status !== PaymentStatus::PENDING) {
            throw new \InvalidArgumentException(
                "Hanya pembayaran berstatus [pending] yang dapat ditolak."
            );
        }

        $payment->update([
            'status'           => PaymentStatus::REJECTED,
            'rejection_reason' => $reason,
            'confirmed_by'     => auth()->id(),
            'confirmed_at'     => now(),
        ]);

        return $payment->refresh();
    }

    // =========================================================================
    //  MANUAL FLOW: Admin inputs a cash payment directly
    // =========================================================================

    /**
     * Admin records a manual (cash) payment — immediately confirmed.
     * No proof upload required; status is set to `confirmed` immediately.
     *
     * @param  Invoice  $invoice
     * @param  float    $amount
     * @param  string   $notes   Optional notes (e.g. receipt number or remarks)
     * @return Payment
     *
     * @throws \InvalidArgumentException if invoice is already paid or cancelled.
     */
    public function recordManual(Invoice $invoice, float $amount, string $notes = ''): Payment
    {
        $this->ensureInvoiceIsPayable($invoice);

        return DB::transaction(function () use ($invoice, $amount, $notes) {
            $payment = Payment::create([
                'invoice_id'         => $invoice->id,
                'confirmed_by'       => auth()->id(),
                'amount'             => $amount,
                'confirmed_amount'   => $amount,
                'payment_proof_path' => null,
                'status'             => PaymentStatus::CONFIRMED,
                'rejection_reason'   => $notes ?: null,
                'input_method'       => 'manual',
                'confirmed_at'       => now(),
            ]);

            $this->recalculateInvoice($invoice);

            return $payment;
        });
    }

    // =========================================================================
    //  INTERNAL: Recalculate invoice status after payment changes
    // =========================================================================

    /**
     * Recalculate `paid_amount` and `status` on an invoice based on all
     * confirmed payments. Then trigger enrollment status advance if fully paid.
     */
    private function recalculateInvoice(Invoice $invoice): void
    {
        // Sum only confirmed payments
        $totalPaid = Payment::where('invoice_id', $invoice->id)
            ->where('status', PaymentStatus::CONFIRMED->value)
            ->sum('confirmed_amount');

        $newStatus = match(true) {
            $totalPaid <= 0                           => InvoiceStatus::UNPAID,
            $totalPaid < $invoice->total_amount       => InvoiceStatus::PARTIAL,
            default                                   => InvoiceStatus::PAID,
        };

        $invoice->update([
            'paid_amount' => $totalPaid,
            'status'      => $newStatus,
        ]);

        // Advance enrollment status automatically when invoice is fully paid
        if ($newStatus === InvoiceStatus::PAID) {
            $this->advanceEnrollmentOnPayment($invoice);
        }
    }

    /**
     * Advance the enrollment to the next logical status when the related invoice is paid.
     *
     * Mapping:
     *   invoice.category = 'registration'   → waiting_payment_reg → verified_reg
     *   invoice.category = 're_registration' → waiting_payment_final → settled
     */
    private function advanceEnrollmentOnPayment(Invoice $invoice): void
    {
        $enrollment = $invoice->enrollment;

        $nextStatus = match($invoice->category) {
            'registration'    => EnrollmentStatus::VERIFIED_REG,
            're_registration' => EnrollmentStatus::SETTLED,
            default           => null,
        };

        if ($nextStatus && $this->stateMachine->canTransition($enrollment->status, $nextStatus)) {
            $this->stateMachine->transition(
                $enrollment,
                $nextStatus,
                "Status otomatis berubah setelah invoice #{$invoice->invoice_number} lunas."
            );
        }
    }

    // =========================================================================
    //  GUARD
    // =========================================================================

    /**
     * Ensure an invoice can still receive payments.
     */
    private function ensureInvoiceIsPayable(Invoice $invoice): void
    {
        if ($invoice->status === InvoiceStatus::PAID) {
            throw new \InvalidArgumentException(
                "Invoice #{$invoice->invoice_number} sudah lunas. Tidak dapat menambah pembayaran baru."
            );
        }

        if ($invoice->status === InvoiceStatus::CANCELLED) {
            throw new \InvalidArgumentException(
                "Invoice #{$invoice->invoice_number} telah dibatalkan."
            );
        }
    }
}
