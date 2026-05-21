<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $admin = DB::table('users')->where('username', 'developer')->first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Get enrollments with applicant names
        $enrollments = DB::table('applicant_enrollments')
            ->join('applicants', 'applicant_enrollments.applicant_id', '=', 'applicants.id')
            ->select('applicant_enrollments.id', 'applicants.full_name', 'applicants.user_id')
            ->get();

        foreach ($enrollments as $enrollment) {
            $applicantUserId = $enrollment->user_id ?? $adminId; // Fallback to admin if manual input Rian
            
            if ($enrollment->full_name === 'Budi Santoso') {
                // Log 1: draft -> registered
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $applicantUserId,
                    'from_status' => 'draft',
                    'to_status' => 'registered',
                    'reason' => 'Formulir disubmit pendaftar.',
                    'changed_at' => $now->copy()->subDays(5),
                ]);
                
                // Log 2: registered -> waiting_payment_reg
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $adminId,
                    'from_status' => 'registered',
                    'to_status' => 'waiting_payment_reg',
                    'reason' => 'Invoice pendaftaran di-generate otomatis.',
                    'changed_at' => $now->copy()->subDays(5)->addMinutes(5),
                ]);

                // Log 3: waiting_payment_reg -> verified_reg
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $adminId,
                    'from_status' => 'waiting_payment_reg',
                    'to_status' => 'verified_reg',
                    'reason' => 'Pembayaran pendaftaran dikonfirmasi admin.',
                    'changed_at' => $now->copy()->subDays(3),
                ]);
            }
            
            if ($enrollment->full_name === 'Siti Aminah') {
                // Log 1: draft -> registered
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $applicantUserId,
                    'from_status' => 'draft',
                    'to_status' => 'registered',
                    'reason' => 'Formulir disubmit pendaftar.',
                    'changed_at' => $now->copy()->subDays(2),
                ]);
                
                // Log 2: registered -> waiting_payment_reg
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $adminId,
                    'from_status' => 'registered',
                    'to_status' => 'waiting_payment_reg',
                    'reason' => 'Invoice pendaftaran di-generate otomatis.',
                    'changed_at' => $now->copy()->subDays(2)->addMinutes(10),
                ]);
            }
            
            if ($enrollment->full_name === 'Rian Hidayat') {
                // Log 1: draft -> registered
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $adminId, // manual input
                    'from_status' => 'draft',
                    'to_status' => 'registered',
                    'reason' => 'Pendaftaran diinput manual oleh admin.',
                    'changed_at' => $now->copy()->subDays(10),
                ]);
                
                // Log 2: registered -> verified_reg
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $adminId,
                    'from_status' => 'registered',
                    'to_status' => 'verified_reg',
                    'reason' => 'Jalur Prestasi gratis biaya pendaftaran. Terverifikasi otomatis.',
                    'changed_at' => $now->copy()->subDays(10)->addMinutes(1),
                ]);

                // Log 3: verified_reg -> in_review
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $adminId,
                    'from_status' => 'verified_reg',
                    'to_status' => 'in_review',
                    'reason' => 'Berkas pendaftaran mulai direview.',
                    'changed_at' => $now->copy()->subDays(8),
                ]);

                // Log 4: in_review -> passed
                DB::table('status_logs')->insert([
                    'enrollment_id' => $enrollment->id,
                    'changed_by' => $adminId,
                    'from_status' => 'in_review',
                    'to_status' => 'passed',
                    'reason' => 'Lulus seleksi berkas prestasi akademik tingkat nasional.',
                    'changed_at' => $now->copy()->subDays(4),
                ]);
            }
        }
    }
}
