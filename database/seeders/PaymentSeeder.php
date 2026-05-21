<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $admin = DB::table('users')->where('username', 'developer')->first();
        $adminId = $admin ? $admin->id : null;

        // 1. Get Invoices
        $budiInvoice = DB::table('invoices')
            ->where('invoice_number', 'INV-2026-000001')
            ->first();
            
        $sitiInvoice = DB::table('invoices')
            ->where('invoice_number', 'INV-2026-000002')
            ->first();

        // 2. Confirmed Payment for Budi
        if ($budiInvoice) {
            DB::table('payments')->insert([
                'invoice_id' => $budiInvoice->id,
                'confirmed_by' => $adminId,
                'amount' => $budiInvoice->total_amount,
                'confirmed_amount' => $budiInvoice->total_amount,
                'payment_proof_path' => 'proofs/2026/bukti_budi.jpg',
                'status' => 'confirmed',
                'rejection_reason' => null,
                'input_method' => 'upload',
                'confirmed_at' => $now,
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now,
            ]);
        }

        // 3. Pending Payment for Siti (belum dikonfirmasi)
        if ($sitiInvoice) {
            DB::table('payments')->insert([
                'invoice_id'         => $sitiInvoice->id,
                'confirmed_by'       => null,
                'amount'             => $sitiInvoice->total_amount,
                'confirmed_amount'   => null,
                'payment_proof_path' => 'proofs/2026/bukti_siti.png',
                'status'             => 'pending',
                'rejection_reason'   => null,
                'input_method'       => 'upload',
                'confirmed_at'       => null,
                'created_at'         => $now->copy()->subMinutes(30),
                'updated_at'         => $now->copy()->subMinutes(30),
            ]);
        }

        // 4. Confirmed Payment for Ahmad Fauzi (re_registration invoice)
        $ahmadInvoice = DB::table('invoices')
            ->where('invoice_number', 'INV-2026-000004')
            ->first();

        if ($ahmadInvoice) {
            DB::table('payments')->insert([
                'invoice_id'         => $ahmadInvoice->id,
                'confirmed_by'       => $adminId,
                'amount'             => $ahmadInvoice->total_amount,
                'confirmed_amount'   => $ahmadInvoice->total_amount,
                'payment_proof_path' => 'proofs/2026/bukti_ahmad.jpg',
                'status'             => 'confirmed',
                'rejection_reason'   => null,
                'input_method'       => 'upload',
                'confirmed_at'       => $now->copy()->subDays(2),
                'created_at'         => $now->copy()->subDays(3),
                'updated_at'         => $now->copy()->subDays(2),
            ]);
        }

        // 4. Confirmed Payment for Dewi Rahayu (re_registration invoice)
        $dewiInvoice = DB::table('invoices')
            ->where('invoice_number', 'INV-2026-000005')
            ->first();

        if ($dewiInvoice) {
            DB::table('payments')->insert([
                'invoice_id'         => $dewiInvoice->id,
                'confirmed_by'       => $adminId,
                'amount'             => $dewiInvoice->total_amount,
                'confirmed_amount'   => $dewiInvoice->total_amount,
                'payment_proof_path' => 'proofs/2026/bukti_dewi.jpg',
                'status'             => 'confirmed',
                'rejection_reason'   => null,
                'input_method'       => 'upload',
                'confirmed_at'       => $now->copy()->subDays(5),
                'created_at'         => $now->copy()->subDays(7),
                'updated_at'         => $now->copy()->subDays(5),
            ]);
        }
    }
}
