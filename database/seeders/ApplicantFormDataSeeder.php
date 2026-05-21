<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicantFormDataSeeder extends Seeder
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

        // 2. Populate for Budi (Reguler Track form fields)
        if ($budiEnroll) {
            $fields = DB::table('spmb_track_form_fields')
                ->where('spmb_track_id', $budiEnroll->spmb_track_id)
                ->get();
                
            foreach ($fields as $field) {
                $value = match ($field->field_name) {
                    'asal_sekolah' => 'SMP Negeri 1 Jakarta',
                    'kartu_keluarga' => 'form_data/kk_budi.pdf',
                    default => 'N/A',
                };
                
                DB::table('applicant_form_data')->insert([
                    'enrollment_id' => $budiEnroll->id,
                    'field_id' => $field->id,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 3. Populate for Siti (Reguler Track form fields)
        if ($sitiEnroll) {
            $fields = DB::table('spmb_track_form_fields')
                ->where('spmb_track_id', $sitiEnroll->spmb_track_id)
                ->get();
                
            foreach ($fields as $field) {
                $value = match ($field->field_name) {
                    'asal_sekolah' => 'SMP Islam Al-Azhar Bandung',
                    'kartu_keluarga' => 'form_data/kk_siti.pdf',
                    default => 'N/A',
                };
                
                DB::table('applicant_form_data')->insert([
                    'enrollment_id' => $sitiEnroll->id,
                    'field_id' => $field->id,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 4. Populate for Rian (Prestasi Track form fields)
        if ($rianEnroll) {
            $fields = DB::table('spmb_track_form_fields')
                ->where('spmb_track_id', $rianEnroll->spmb_track_id)
                ->get();
                
            foreach ($fields as $field) {
                $value = match ($field->field_name) {
                    'sertifikat_prestasi' => 'form_data/sertifikat_rian.pdf',
                    default => 'N/A',
                };
                
                DB::table('applicant_form_data')->insert([
                    'enrollment_id' => $rianEnroll->id,
                    'field_id' => $field->id,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
