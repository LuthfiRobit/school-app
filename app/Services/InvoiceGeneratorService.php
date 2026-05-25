<?php

namespace App\Services;

use App\Models\ApplicantEnrollment;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use Illuminate\Support\Facades\DB;

class InvoiceGeneratorService
{
    /**
     * Generate invoice for registration fee.
     */
    public function generateRegistrationInvoice(ApplicantEnrollment $enrollment): Invoice
    {
        return DB::transaction(function () use ($enrollment) {
            $invoiceNumber = $this->generateInvoiceNumber();
            
            // Fetch track registration fee
            $registrationFee = $enrollment->spmbTrack->registration_fee;
            
            $invoice = Invoice::create([
                'enrollment_id' => $enrollment->id,
                'invoice_number' => $invoiceNumber,
                'category' => 'registration',
                'total_amount' => $registrationFee,
                'paid_amount' => 0.00,
                'status' => InvoiceStatus::UNPAID,
                'due_date' => now()->addDays(3),
            ]);
            
            // Add invoice item for registration fee
            DB::table('invoice_items')->insert([
                'invoice_id' => $invoice->id,
                'fee_component_id' => null, // registration fee is direct
                'description' => 'Biaya Pendaftaran Jalur ' . $enrollment->spmbTrack->trackType->name,
                'amount' => $registrationFee,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return $invoice;
        });
    }

    /**
     * Generate invoice for re-registration fees.
     */
    public function generateReRegistrationInvoice(ApplicantEnrollment $enrollment): Invoice
    {
        return DB::transaction(function () use ($enrollment) {
            $invoiceNumber = $this->generateInvoiceNumber();
            
            // Fetch track fee mappings
            $trackFees = DB::table('spmb_track_fees')
                ->where('spmb_track_id', $enrollment->spmb_track_id)
                ->where('category', 're_registration')
                ->get();
                
            $totalAmount = $trackFees->sum('amount');
            
            // Check if payment mode is POST_PAYMENT, if so add registration fee
            $hasRegistrationFee = false;
            $registrationFee = 0;
            if ($enrollment->spmbTrack->payment_mode === \App\Enums\PaymentMode::POST_PAYMENT) {
                // Find if there is a registration fee in spmb_track_fees
                $regFees = DB::table('spmb_track_fees')
                    ->where('spmb_track_id', $enrollment->spmb_track_id)
                    ->where('category', 'registration')
                    ->get();
                    
                if ($regFees->isNotEmpty()) {
                    $totalAmount += $regFees->sum('amount');
                    // We'll merge them for invoice_items iteration
                    $trackFees = $trackFees->merge($regFees);
                } else {
                    // Fallback to track registration_fee if not mapped in spmb_track_fees
                    $registrationFee = $enrollment->spmbTrack->registration_fee;
                    if ($registrationFee > 0) {
                        $totalAmount += $registrationFee;
                        $hasRegistrationFee = true;
                    }
                }
            }
            
            $invoice = Invoice::create([
                'enrollment_id' => $enrollment->id,
                'invoice_number' => $invoiceNumber,
                'category' => 're_registration',
                'total_amount' => $totalAmount,
                'paid_amount' => 0.00,
                'status' => InvoiceStatus::UNPAID,
                'due_date' => now()->addDays(7),
            ]);
            
            // Add invoice items
            foreach ($trackFees as $fee) {
                DB::table('invoice_items')->insert([
                    'invoice_id' => $invoice->id,
                    'fee_component_id' => $fee->master_fee_component_id,
                    'description' => DB::table('master_fee_components')->where('id', $fee->master_fee_component_id)->value('name') ?? 'Komponen Biaya',
                    'amount' => $fee->amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Add fallback registration fee if it was from spmb_tracks
            if ($hasRegistrationFee) {
                DB::table('invoice_items')->insert([
                    'invoice_id' => $invoice->id,
                    'fee_component_id' => null,
                    'description' => 'Biaya Pendaftaran Jalur ' . $enrollment->spmbTrack->trackType->name,
                    'amount' => $registrationFee,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            return $invoice;
        });
    }

    /**
     * Generate a unique sequential invoice number: INV-{YEAR}-{SEQUENCE}
     * Uses DB max+1 within a transaction — safe for low-concurrency SPMB context.
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');

        // Count existing invoices this year to derive a safe sequential number
        $count = DB::table('invoices')
            ->whereYear('created_at', $year)
            ->lockForUpdate()
            ->count();

        $sequence = str_pad($count + 1, 6, '0', STR_PAD_LEFT);

        return "INV-{$year}-{$sequence}";
    }

    /**
     * Check if enrollment is eligible for finalization.
     */
    public function isEligibleForFinalization(ApplicantEnrollment $enrollment): bool
    {
        $invoice = $enrollment->invoices()->where('category', 're_registration')->first();
        if (!$invoice) return false;
        return $invoice->paid_amount >= $invoice->total_amount;
    }
}

