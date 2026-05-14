<?php

namespace Database\Seeders;

use App\Models\MasterAssessmentType;
use Illuminate\Database\Seeder;

class MasterAssessmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Tes Tulis (Akademik)',
                'input_type' => 'score',
                'description' => 'Penilaian berbasis skor angka (0-100) untuk mengukur kemampuan akademik dasar.',
                'is_active' => true,
            ],
            [
                'name' => 'Tes Wawancara',
                'input_type' => 'score',
                'description' => 'Penilaian kepribadian dan komitmen orang tua/siswa melalui wawancara tatap muka.',
                'is_active' => true,
            ],
            [
                'name' => 'Tes Tahfidz/Baca Al-Qur\'an',
                'input_type' => 'pass_fail',
                'description' => 'Verifikasi kemampuan baca tulis Al-Qur\'an atau hafalan surat tertentu.',
                'is_active' => true,
            ],
            [
                'name' => 'Tes Kesehatan/Fisik',
                'input_type' => 'pass_fail',
                'description' => 'Penilaian kelayakan fisik dan kesehatan calon siswa.',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            MasterAssessmentType::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
