<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AcademicYearSeeder::class,
            SchoolIdentitySeeder::class,
            RoleSeeder::class,
            BankAccountSeeder::class,
            MasterTrackTypeSeeder::class,
            MasterAssessmentTypeSeeder::class,
            MasterFeeComponentSeeder::class,
            SpmbConfigurationSeeder::class,
            SpmbTrackSeeder::class,
            ApplicantSeeder::class,
            ApplicantEnrollmentSeeder::class,
            ApplicantFormDataSeeder::class,
            InvoiceSeeder::class,
            PaymentSeeder::class,
            AssessmentSeeder::class,
            StatusLogSeeder::class,
        ]);
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
