<?php

namespace Database\Seeders;

use App\Models\SchoolIdentity;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolIdentitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : null;

        SchoolIdentity::updateOrCreate(
            ['id' => 1],
            [
                'school_name' => 'SMA Negeri 1 Contoh',
                'npsn' => '12345678',
                'education_level' => 'SMA',
                'school_status' => 'Negeri',
                'ownership_status' => 'Pemerintah Daerah',
                'establishment_sk' => '421/001/SK/2000',
                'establishment_date' => '2000-01-01',
                'operational_sk' => '421.3/999/DISDIK/2005',
                'tax_id' => '01.234.567.8-001.000',
                'accreditation' => 'A',
                'accreditation_expiry_date' => '2028-12-31',
                'address' => 'Jl. Pendidikan No. 1, Kota Pelajar, Provinsi Indonesia',
                'latitude' => '-6.175392',
                'longitude' => '106.827153',
                'whatsapp' => '6281234567890',
                'phone' => '021-1234567',
                'email' => 'admin@sman1contoh.sch.id',
                'website' => 'https://www.sman1contoh.sch.id',
                'headmaster_name' => 'Dr. Budi Santoso, M.Pd.',
                'headmaster_nip' => '197501012000011001',
                'treasurer_name' => 'Siti Aminah, S.E.',
                'treasurer_nip' => '198005052005012002',
                'operator_name' => 'Luthfi Hakim',
                'operator_nip' => '1234567890',
                'created_by' => $userId,
            ]
        );
    }
}
