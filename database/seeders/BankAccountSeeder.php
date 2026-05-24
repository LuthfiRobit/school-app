<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'developer')->first() ?? User::first();
        $userId = $user ? $user->id : null;

        $accounts = [
            [
                'bank_name' => 'BCA',
                'account_number' => '1234567890',
                'account_holder' => 'Yayasan Tunas Luhur',
                'branch' => 'KCU Probolinggo',
                'description' => 'Rekening utama untuk pendaftaran siswa baru (SPMB)',
                'logo_path' => 'logos/bca.png',
                'is_active' => 1,
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'account_number' => '9876543210',
                'account_holder' => 'Yayasan Tunas Luhur',
                'branch' => 'KC Kraksaan',
                'description' => 'Rekening alternatif untuk pendaftaran dan SPP',
                'logo_path' => 'logos/mandiri.png',
                'is_active' => 1,
            ],
            [
                'bank_name' => 'BSI',
                'account_number' => '5556667778',
                'account_holder' => 'Yayasan Tunas Luhur',
                'branch' => 'KC Paiton',
                'description' => 'Rekening untuk pembayaran daftar ulang dan biaya lainnya',
                'logo_path' => 'logos/bsi.png',
                'is_active' => 1,
            ],
        ];

        foreach ($accounts as $account) {
            BankAccount::updateOrCreate(
                ['bank_name' => $account['bank_name'], 'account_number' => $account['account_number']],
                array_merge($account, [
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ])
            );
        }
    }
}
