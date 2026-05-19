<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpmbTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temukan konfigurasi SPMB aktif pertama menggunakan Query Builder
        $spmbConfig = DB::table('spmb_configurations')->where('status', 'active')->first();

        if (!$spmbConfig) {
            $this->command->warn('Tidak ada SPMB Configuration yang aktif. Seeder SpmbTrack dilewati.');
            return;
        }

        DB::beginTransaction();

        try {
            // Ambil beberapa master data
            $trackReguler = DB::table('master_track_types')->where('slug', 'jalur-reguler')->first();
            $trackPrestasi = DB::table('master_track_types')->where('slug', 'jalur-prestasi')->first();

            $feeRegistration = DB::table('master_fee_components')->where('category', 'registration')->first();
            $feeReRegistration1 = DB::table('master_fee_components')->where('category', 're_registration')->first();
            
            $assessmentTest = DB::table('master_assessment_types')->where('name', 'LIKE', '%Tulis%')->first();
            $assessmentInterview = DB::table('master_assessment_types')->where('name', 'LIKE', '%Wawancara%')->first();

            // 1. Buat SpmbTrack untuk Reguler
            if ($trackReguler) {
                DB::table('spmb_tracks')->updateOrInsert(
                    [
                        'spmb_configuration_id' => $spmbConfig->id,
                        'master_track_type_id' => $trackReguler->id,
                    ],
                    [
                        'quota' => 100,
                        'registration_fee' => 250000,
                        'payment_mode' => 'PRE_PAYMENT',
                        'allow_carryover' => false,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // Ambil ID track yang baru dibuat/diupdate
                $spmbTrackReg = DB::table('spmb_tracks')
                    ->where('spmb_configuration_id', $spmbConfig->id)
                    ->where('master_track_type_id', $trackReguler->id)
                    ->first();

                // Tambahkan Fee
                if ($feeRegistration) {
                    DB::table('spmb_track_fees')->updateOrInsert([
                        'spmb_track_id' => $spmbTrackReg->id,
                        'master_fee_component_id' => $feeRegistration->id,
                    ], [
                        'amount' => 250000,
                        'category' => 'registration',
                        'display_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if ($feeReRegistration1) {
                    DB::table('spmb_track_fees')->updateOrInsert([
                        'spmb_track_id' => $spmbTrackReg->id,
                        'master_fee_component_id' => $feeReRegistration1->id,
                    ], [
                        'amount' => 5000000,
                        'category' => 're_registration',
                        'display_order' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Tambahkan Assessment
                if ($assessmentTest) {
                    DB::table('spmb_track_assessments')->updateOrInsert([
                        'spmb_track_id' => $spmbTrackReg->id,
                        'master_assessment_type_id' => $assessmentTest->id,
                    ], [
                        'weight' => 60,
                        'passing_score' => 70,
                        'display_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if ($assessmentInterview) {
                    DB::table('spmb_track_assessments')->updateOrInsert([
                        'spmb_track_id' => $spmbTrackReg->id,
                        'master_assessment_type_id' => $assessmentInterview->id,
                    ], [
                        'weight' => 40,
                        'passing_score' => 60,
                        'display_order' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Tambahkan Form Fields
                DB::table('spmb_track_form_fields')->updateOrInsert([
                    'spmb_track_id' => $spmbTrackReg->id,
                    'field_name' => 'asal_sekolah',
                ], [
                    'field_label' => 'Asal Sekolah',
                    'field_type' => 'text',
                    'is_required' => true,
                    'display_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('spmb_track_form_fields')->updateOrInsert([
                    'spmb_track_id' => $spmbTrackReg->id,
                    'field_name' => 'kartu_keluarga',
                ], [
                    'field_label' => 'Upload Kartu Keluarga',
                    'field_type' => 'file',
                    'is_required' => true,
                    'display_order' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2. Buat SpmbTrack untuk Prestasi
            if ($trackPrestasi) {
                DB::table('spmb_tracks')->updateOrInsert(
                    [
                        'spmb_configuration_id' => $spmbConfig->id,
                        'master_track_type_id' => $trackPrestasi->id,
                    ],
                    [
                        'quota' => 20,
                        'registration_fee' => 0, // Gratis
                        'payment_mode' => 'POST_PAYMENT',
                        'allow_carryover' => false,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $spmbTrackPres = DB::table('spmb_tracks')
                    ->where('spmb_configuration_id', $spmbConfig->id)
                    ->where('master_track_type_id', $trackPrestasi->id)
                    ->first();

                // Fee (Hanya daftar ulang)
                if ($feeReRegistration1) {
                    DB::table('spmb_track_fees')->updateOrInsert([
                        'spmb_track_id' => $spmbTrackPres->id,
                        'master_fee_component_id' => $feeReRegistration1->id,
                    ], [
                        'amount' => 2500000, // Diskon
                        'category' => 're_registration',
                        'display_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Form Fields
                DB::table('spmb_track_form_fields')->updateOrInsert([
                    'spmb_track_id' => $spmbTrackPres->id,
                    'field_name' => 'sertifikat_prestasi',
                ], [
                    'field_label' => 'Sertifikat Prestasi (Minimal Tingkat Kabupaten)',
                    'field_type' => 'file',
                    'is_required' => true,
                    'display_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            $this->command->info('SpmbTrackSeeder berhasil dijalankan.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error pada SpmbTrackSeeder: ' . $e->getMessage());
        }
    }
}
