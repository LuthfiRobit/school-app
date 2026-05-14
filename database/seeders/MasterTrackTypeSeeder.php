<?php

namespace Database\Seeders;

use App\Models\MasterTrackType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterTrackTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracks = [
            [
                'name' => 'Jalur Reguler',
                'description' => 'Pendaftaran melalui jalur umum/reguler dengan seleksi nilai raport dan tes masuk.',
            ],
            [
                'name' => 'Jalur Prestasi / Tahfidz',
                'description' => 'Pendaftaran khusus bagi calon siswa yang memiliki prestasi akademik/non-akademik atau hafalan Al-Qur\'an.',
            ],
            [
                'name' => 'Jalur Mutasi',
                'description' => 'Pendaftaran bagi calon siswa pindahan dari sekolah lain.',
            ],
        ];

        foreach ($tracks as $track) {
            MasterTrackType::updateOrCreate(
                ['slug' => Str::slug($track['name'])],
                [
                    'name' => $track['name'],
                    'description' => $track['description'],
                    'is_active' => true,
                    'created_by' => 1, // Assuming ID 1 is developer/admin
                ]
            );
        }
    }
}
