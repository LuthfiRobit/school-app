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
            if ($enrollment->full_name === 'Ahmad Fauzi') {
                // Ahmad: passed → waiting_payment_final → settled
                $logs = [
                    ['draft',                 'registered',           'Pendaftaran diinput manual oleh admin.',                        14],
                    ['registered',            'verified_reg',         'Jalur Reguler - berkas terverifikasi.',                         13],
                    ['verified_reg',          'in_review',            'Berkas mulai direview panitia.',                                12],
                    ['in_review',             'passed',               'Lulus seleksi administrasi jalur Reguler.',                     10],
                    ['passed',                'waiting_payment_final','Invoice daftar ulang di-generate otomatis.',                    10],
                    ['waiting_payment_final', 'settled',              'Pembayaran daftar ulang Rp 5.000.000 dikonfirmasi admin.',        3],
                ];
                foreach ($logs as $i => [$from, $to, $reason, $daysAgo]) {
                    DB::table('status_logs')->insert([
                        'enrollment_id' => $enrollment->id,
                        'changed_by'    => $adminId,
                        'from_status'   => $from,
                        'to_status'     => $to,
                        'reason'        => $reason,
                        'changed_at'    => $now->copy()->subDays($daysAgo)->addMinutes($i * 5),
                    ]);
                }
            }

            if ($enrollment->full_name === 'Dewi Rahayu') {
                // Dewi: passed → waiting_payment_final → settled → permanent_student
                $logs = [
                    ['draft',                 'registered',           'Pendaftaran diinput manual oleh admin.',                        20],
                    ['registered',            'verified_reg',         'Jalur Prestasi - berkas terverifikasi.',                        19],
                    ['verified_reg',          'in_review',            'Berkas mulai direview panitia.',                                18],
                    ['in_review',             'passed',               'Lulus seleksi jalur Prestasi - juara OSN Fisika.',              15],
                    ['passed',                'waiting_payment_final','Invoice daftar ulang di-generate otomatis.',                    15],
                    ['waiting_payment_final', 'settled',              'Pembayaran daftar ulang Rp 2.500.000 dikonfirmasi admin.',        8],
                    ['settled',               'permanent_student',    'Finalisasi daftar ulang - Dewi Rahayu resmi menjadi Siswa Tetap.', 5],
                ];
                foreach ($logs as $i => [$from, $to, $reason, $daysAgo]) {
                    DB::table('status_logs')->insert([
                        'enrollment_id' => $enrollment->id,
                        'changed_by'    => $adminId,
                        'from_status'   => $from,
                        'to_status'     => $to,
                        'reason'        => $reason,
                        'changed_at'    => $now->copy()->subDays($daysAgo)->addMinutes($i * 5),
                    ]);
                }
            }
        }
    }
}
