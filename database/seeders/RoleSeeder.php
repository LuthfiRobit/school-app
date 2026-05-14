<?php

namespace Database\Seeders;

use App\Enums\RoleScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $now = Carbon::now();

        // Daftar Roles berdasarkan Scope
        $roles = [
            // SCOPE: SYSTEM (Akses mutlak arsitektur aplikasi)
            ['name' => 'Developer', 'scope' => RoleScope::SYSTEM->value, 'guard_name' => 'web'],

            // SCOPE: PIMPINAN (Pemegang SK tertinggi satuan pendidikan)
            ['name' => 'Kepala Sekolah', 'scope' => RoleScope::PIMPINAN->value, 'guard_name' => 'web'],

            // SCOPE: MANAJEMEN (Wakil Kepala Sekolah / Struktur Manajemen Atas)
            ['name' => 'Waka Kurikulum', 'scope' => RoleScope::MANAJEMEN->value, 'guard_name' => 'web'],
            ['name' => 'Waka Kesiswaan', 'scope' => RoleScope::MANAJEMEN->value, 'guard_name' => 'web'],
            ['name' => 'Waka Sarana Prasarana', 'scope' => RoleScope::MANAJEMEN->value, 'guard_name' => 'web'],
            ['name' => 'Waka Humas', 'scope' => RoleScope::MANAJEMEN->value, 'guard_name' => 'web'],

            // SCOPE: GURU (Tenaga Pendidik & Tugas Tambahan Akademik)
            ['name' => 'Guru Mata Pelajaran', 'scope' => RoleScope::GURU->value, 'guard_name' => 'web'],
            ['name' => 'Guru BK', 'scope' => RoleScope::GURU->value, 'guard_name' => 'web'],
            ['name' => 'Wali Kelas', 'scope' => RoleScope::GURU->value, 'guard_name' => 'web'],
            ['name' => 'Pembina Ekstrakurikuler', 'scope' => RoleScope::GURU->value, 'guard_name' => 'web'],
            ['name' => 'Ketua Program Keahlian', 'scope' => RoleScope::GURU->value, 'guard_name' => 'web'],
            ['name' => 'Guru Pendamping Khusus', 'scope' => RoleScope::GURU->value, 'guard_name' => 'web'],

            // SCOPE: TENDIK (Tenaga Kependidikan & Administrasi)
            ['name' => 'Kepala Tata Usaha', 'scope' => RoleScope::TENDIK->value, 'guard_name' => 'web'],
            ['name' => 'Staf Administrasi TU', 'scope' => RoleScope::TENDIK->value, 'guard_name' => 'web'],
            ['name' => 'Operator Dapodik', 'scope' => RoleScope::TENDIK->value, 'guard_name' => 'web'],
            ['name' => 'Pustakawan', 'scope' => RoleScope::TENDIK->value, 'guard_name' => 'web'],
            ['name' => 'Laboran', 'scope' => RoleScope::TENDIK->value, 'guard_name' => 'web'],
            ['name' => 'Bendahara BOS', 'scope' => RoleScope::TENDIK->value, 'guard_name' => 'web'],
            ['name' => 'Bendahara Komite', 'scope' => RoleScope::TENDIK->value, 'guard_name' => 'web'],

            // SCOPE: SISWA (Peserta Didik)
            ['name' => 'Siswa Reguler', 'scope' => RoleScope::SISWA->value, 'guard_name' => 'web'],
            ['name' => 'Pengurus OSIS', 'scope' => RoleScope::SISWA->value, 'guard_name' => 'web'],
            ['name' => 'Ketua Kelas', 'scope' => RoleScope::SISWA->value, 'guard_name' => 'web'],
        ];

        // Insert atau Update Roles
        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name'], 'guard_name' => $role['guard_name']],
                [
                    'scope' => $role['scope'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // ------------------------------------------------------------------
        // ASSIGN AKUN DEVELOPER KE ROLE
        // ------------------------------------------------------------------
        
        // 1. Buat/Update User Developer
        DB::table('users')->updateOrInsert(
            ['username' => 'developer'],
            [
                'name' => 'System Developer',
                'email' => 'dev@school.id',
                'password' => Hash::make('password'), // Ubah sesuai kebutuhan
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // 2. Ambil ID User dan Role
        $user = DB::table('users')->where('username', 'developer')->first();
        $role = DB::table('roles')->where('name', 'Developer')->first();

        // 3. Assign Role ke User (Model Has Roles)
        if ($user && $role) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $role->id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                ],
                []
            );
        }
    }
}
