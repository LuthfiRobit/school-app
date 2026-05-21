<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        
        // 1. Get enrollments
        $budiEnroll = DB::table('applicant_enrollments')
            ->join('applicants', 'applicant_enrollments.applicant_id', '=', 'applicants.id')
            ->where('applicants.full_name', 'Budi Santoso')
            ->select('applicant_enrollments.id', 'applicant_enrollments.spmb_track_id')
            ->first();
            
        $sitiEnroll = DB::table('applicant_enrollments')
            ->join('applicants', 'applicant_enrollments.applicant_id', '=', 'applicants.id')
            ->where('applicants.full_name', 'Siti Aminah')
            ->select('applicant_enrollments.id', 'applicant_enrollments.spmb_track_id')
            ->first();
            
        $rianEnroll = DB::table('applicant_enrollments')
            ->join('applicants', 'applicant_enrollments.applicant_id', '=', 'applicants.id')
            ->where('applicants.full_name', 'Rian Hidayat')
            ->select('applicant_enrollments.id', 'applicant_enrollments.spmb_track_id')
            ->first();

        // 2. Invoice for Budi (Paid Registration Fee)
        if ($budiEnroll) {
            $registrationFee = DB::table('spmb_tracks')
                ->where('id', $budiEnroll->spmb_track_id)
                ->value('registration_fee') ?? 250000.00;
                
            $invoiceId = DB::table('invoices')->insertGetId([
                'enrollment_id' => $budiEnroll->id,
                'invoice_number' => 'INV-2026-000001',
                'category' => 'registration',
                'total_amount' => $registrationFee,
                'paid_amount' => $registrationFee,
                'status' => 'paid',
                'due_date' => $now->copy()->addDays(3),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('invoice_items')->insert([
                'invoice_id' => $invoiceId,
                'fee_component_id' => null,
                'description' => 'Biaya Pendaftaran Jalur Reguler',
                'amount' => $registrationFee,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 3. Invoice for Siti (Unpaid Registration Fee)
        if ($sitiEnroll) {
            $registrationFee = DB::table('spmb_tracks')
                ->where('id', $sitiEnroll->spmb_track_id)
                ->value('registration_fee') ?? 250000.00;
                
            $invoiceId = DB::table('invoices')->insertGetId([
                'enrollment_id' => $sitiEnroll->id,
                'invoice_number' => 'INV-2026-000002',
                'category' => 'registration',
                'total_amount' => $registrationFee,
                'paid_amount' => 0.00,
                'status' => 'unpaid',
                'due_date' => $now->copy()->addDays(3),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('invoice_items')->insert([
                'invoice_id' => $invoiceId,
                'fee_component_id' => null,
                'description' => 'Biaya Pendaftaran Jalur Reguler',
                'amount' => $registrationFee,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 4. Invoice for Rian (Unpaid Re-Registration Fee - passed status)
        if ($rianEnroll) {
            $trackFees = DB::table('spmb_track_fees')
                ->where('spmb_track_id', $rianEnroll->spmb_track_id)
                ->where('category', 're_registration')
                ->get();
                
            $totalAmount = $trackFees->sum('amount');
            if ($totalAmount == 0) {
                $totalAmount = 2500000.00; // fallback default diskon prestasi
            }
                
            $invoiceId = DB::table('invoices')->insertGetId([
                'enrollment_id' => $rianEnroll->id,
                'invoice_number' => 'INV-2026-000003',
                'category' => 're_registration',
                'total_amount' => $totalAmount,
                'paid_amount' => 0.00,
                'status' => 'unpaid',
                'due_date' => $now->copy()->addDays(7),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($trackFees->isNotEmpty()) {
                foreach ($trackFees as $fee) {
                    $componentName = DB::table('master_fee_components')
                        ->where('id', $fee->master_fee_component_id)
                        ->value('name') ?? 'Biaya Daftar Ulang';
                        
                    DB::table('invoice_items')->insert([
                        'invoice_id' => $invoiceId,
                        'fee_component_id' => $fee->master_fee_component_id,
                        'description' => $componentName,
                        'amount' => $fee->amount,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            } else {
                DB::table('invoice_items')->insert([
                    'invoice_id' => $invoiceId,
                    'fee_component_id' => null,
                    'description' => 'Uang Pangkal / Gedung (Jalur Prestasi)',
                    'amount' => $totalAmount,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
