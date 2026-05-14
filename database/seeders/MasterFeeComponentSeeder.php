<?php

namespace Database\Seeders;

use App\Models\MasterFeeComponent;
use Illuminate\Database\Seeder;

class MasterFeeComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Uang Pangkal / Biaya Masuk',
                'category' => 're_registration',
                'description' => 'Biaya gedung dan pengembangan institusi bagi siswa baru.',
                'is_active' => true,
            ],
            [
                'name' => 'Biaya Formulir Pendaftaran',
                'category' => 'registration',
                'description' => 'Biaya administrasi dan pembelian formulir pendaftaran awal.',
                'is_active' => true,
            ],
            [
                'name' => 'Biaya Seragam Sekolah',
                'category' => 're_registration',
                'description' => 'Paket seragam lengkap (OSIS, Pramuka, Olahraga, dan Batik).',
                'is_active' => true,
            ],
            [
                'name' => 'Biaya Psikotes / Tes Penempatan',
                'category' => 'registration',
                'description' => 'Biaya pelaksanaan tes minat bakat dan penempatan kelas.',
                'is_active' => true,
            ],
            [
                'name' => 'SPP Bulan Juli',
                'category' => 're_registration',
                'description' => 'Pembayaran uang sekolah bulan pertama (awal tahun ajaran).',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            MasterFeeComponent::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
