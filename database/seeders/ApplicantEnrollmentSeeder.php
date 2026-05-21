<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicantEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $adminId = DB::table('users')->where('username', 'developer')->value('id');

        // Get Applicants
        $budi  = DB::table('applicants')->where('full_name', 'Budi Santoso')->first();
        $siti  = DB::table('applicants')->where('full_name', 'Siti Aminah')->first();
        $rian  = DB::table('applicants')->where('full_name', 'Rian Hidayat')->first();
        $ahmad = DB::table('applicants')->where('full_name', 'Ahmad Fauzi')->first();
        $dewi  = DB::table('applicants')->where('full_name', 'Dewi Rahayu')->first();

        // Get active configuration
        $config = DB::table('spmb_configurations')->where('status', 'active')->first();
        if (!$config) {
            $this->command->warn('Tidak ada SPMB Configuration aktif. EnrollmentSeeder dilewati.');
            return;
        }

        // Get Tracks
        $trackReguler = DB::table('spmb_tracks')
            ->where('spmb_configuration_id', $config->id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('master_track_types')
                    ->whereColumn('master_track_types.id', 'spmb_tracks.master_track_type_id')
                    ->where('slug', 'jalur-reguler');
            })->first();

        $trackPrestasi = DB::table('spmb_tracks')
            ->where('spmb_configuration_id', $config->id)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('master_track_types')
                    ->whereColumn('master_track_types.id', 'spmb_tracks.master_track_type_id')
                    ->where('slug', 'jalur-prestasi-tahfidz'); // slug aktual di master_track_types
            })->first();

        // 1. Budi Santoso — status: verified_reg (menunggu review)
        if ($budi && $trackReguler) {
            DB::table('applicant_enrollments')->insert([
                'applicant_id'      => $budi->id,
                'spmb_track_id'     => $trackReguler->id,
                'enrollment_number' => 'SPMB-2026-01-0001',
                'status'            => 'verified_reg',
                'created_by'        => $budi->user_id,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }

        // 2. Siti Aminah — status: waiting_payment_reg (menunggu bayar pendaftaran)
        if ($siti && $trackReguler) {
            DB::table('applicant_enrollments')->insert([
                'applicant_id'      => $siti->id,
                'spmb_track_id'     => $trackReguler->id,
                'enrollment_number' => 'SPMB-2026-01-0002',
                'status'            => 'waiting_payment_reg',
                'created_by'        => $siti->user_id,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }

        // 3. Rian Hidayat — status: passed (baru lulus, belum bayar daftar ulang)
        if ($rian && $trackPrestasi) {
            DB::table('applicant_enrollments')->insert([
                'applicant_id'      => $rian->id,
                'spmb_track_id'     => $trackPrestasi->id,
                'enrollment_number' => 'SPMB-2026-02-0001',
                'status'            => 'passed',
                'created_by'        => $adminId,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);
        }

        // 4. Ahmad Fauzi — status: settled (lunas daftar ulang, siap FINALISASI)
        if ($ahmad && $trackReguler) {
            DB::table('applicant_enrollments')->insert([
                'applicant_id'      => $ahmad->id,
                'spmb_track_id'     => $trackReguler->id,
                'enrollment_number' => 'SPMB-2026-01-0003',
                'status'            => 'settled',
                'created_by'        => $adminId,
                'created_at'        => $now->copy()->subDays(2),
                'updated_at'        => $now,
            ]);
        }

        // 5. Dewi Rahayu — status: permanent_student (siswa tetap, siap UNDUH SURAT)
        if ($dewi && $trackPrestasi) {
            DB::table('applicant_enrollments')->insert([
                'applicant_id'      => $dewi->id,
                'spmb_track_id'     => $trackPrestasi->id,
                'enrollment_number' => 'SPMB-2026-02-0002',
                'status'            => 'permanent_student',
                'created_by'        => $adminId,
                'created_at'        => $now->copy()->subDays(5),
                'updated_at'        => $now,
            ]);
        }
    }
}
