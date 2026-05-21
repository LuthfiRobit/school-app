<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $calonSiswaRole = DB::table('roles')->where('name', 'Calon Siswa')->first();

        // 1. Applicant 1 (linked to a User account)
        $userId1 = DB::table('users')->insertGetId([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@gmail.com',
            'username' => 'budisantoso',
            'password' => Hash::make('password123'),
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($calonSiswaRole) {
            DB::table('model_has_roles')->insert([
                'role_id' => $calonSiswaRole->id,
                'model_type' => 'App\Models\User',
                'model_id' => $userId1,
            ]);
        }

        DB::table('applicants')->insert([
            'user_id' => $userId1,
            'full_name' => 'Budi Santoso',
            'nickname' => 'Budi',
            'nisn' => '0101234567',
            'nik' => '3171012345670001',
            'place_of_birth' => 'Jakarta',
            'date_of_birth' => '2010-06-15',
            'gender' => 'L',
            'religion' => 'Islam',
            'citizenship' => 'WNI',
            'phone' => '081234567890',
            'parent_name' => 'Joko Santoso',
            'parent_phone' => '081234567891',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
            'created_by' => $userId1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Applicant 2 (linked to a User account)
        $userId2 = DB::table('users')->insertGetId([
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@gmail.com',
            'username' => 'sitiaminah',
            'password' => Hash::make('password123'),
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        if ($calonSiswaRole) {
            DB::table('model_has_roles')->insert([
                'role_id' => $calonSiswaRole->id,
                'model_type' => 'App\Models\User',
                'model_id' => $userId2,
            ]);
        }

        DB::table('applicants')->insert([
            'user_id' => $userId2,
            'full_name' => 'Siti Aminah',
            'nickname' => 'Siti',
            'nisn' => '0101234568',
            'nik' => '3273012345670002',
            'place_of_birth' => 'Bandung',
            'date_of_birth' => '2010-08-22',
            'gender' => 'P',
            'religion' => 'Islam',
            'citizenship' => 'WNI',
            'phone' => '082345678901',
            'parent_name' => 'Ahmad',
            'parent_phone' => '082345678902',
            'address' => 'Jl. Mawar No. 45, Bandung',
            'created_by' => $userId2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Applicant 3 (manually input by admin, no user account initially)
        $adminUser = DB::table('users')->where('username', 'developer')->first();
        $adminId = $adminUser ? $adminUser->id : null;

        DB::table('applicants')->insert([
            'user_id' => null,
            'full_name' => 'Rian Hidayat',
            'nickname' => 'Rian',
            'nisn' => '0101234569',
            'nik' => '3578012345670003',
            'place_of_birth' => 'Surabaya',
            'date_of_birth' => '2010-01-05',
            'gender' => 'L',
            'religion' => 'Islam',
            'citizenship' => 'WNI',
            'phone' => '083456789012',
            'parent_name' => 'Supriadi',
            'parent_phone' => '083456789013',
            'address' => 'Jl. Melati No. 7, Surabaya',
            'created_by' => $adminId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
