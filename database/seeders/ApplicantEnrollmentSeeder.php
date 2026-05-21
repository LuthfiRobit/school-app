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
        
        // Get Applicants
        $budi = DB::table('applicants')->where('full_name', 'Budi Santoso')->first();
        $siti = DB::table('applicants')->where('full_name', 'Siti Aminah')->first();
        $rian = DB::table('applicants')->where('full_name', 'Rian Hidayat')->first();
        
        // Get active configuration
        $config = DB::table('spmb_configurations')->where('status', 'active')->first();
        if (!$config) {
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
                    ->where('slug', 'jalur-prestasi');
            })->first();
            
        // Insert enrollments
        if ($budi && $trackReguler) {
            DB::table('applicant_enrollments')->insert([
                'applicant_id' => $budi->id,
                'spmb_track_id' => $trackReguler->id,
                'enrollment_number' => 'SPMB-2026-01-0001',
                'status' => 'verified_reg',
                'created_by' => $budi->user_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        
        if ($siti && $trackReguler) {
            DB::table('applicant_enrollments')->insert([
                'applicant_id' => $siti->id,
                'spmb_track_id' => $trackReguler->id,
                'enrollment_number' => 'SPMB-2026-01-0002',
                'status' => 'waiting_payment_reg',
                'created_by' => $siti->user_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        
        if ($rian && $trackPrestasi) {
            DB::table('applicant_enrollments')->insert([
                'applicant_id' => $rian->id,
                'spmb_track_id' => $trackPrestasi->id,
                'enrollment_number' => 'SPMB-2026-02-0001',
                'status' => 'passed',
                'created_by' => $rian->created_by,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
