<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $userId = $user ? $user->id : null;

        $years = [
            ['name' => '2020/2021', 'start' => '2020-07-01'],
            ['name' => '2021/2022', 'start' => '2021-07-01'],
            ['name' => '2022/2023', 'start' => '2022-07-01'],
            ['name' => '2023/2024', 'start' => '2023-07-01'],
            ['name' => '2024/2025', 'start' => '2024-07-01'],
        ];

        foreach ($years as $index => $y) {
            $isLast = ($index === count($years) - 1);
            $startDate = Carbon::parse($y['start']);
            $endDate = (clone $startDate)->addYear()->subDay();

            // Create Academic Year
            $academicYear = AcademicYear::create([
                'name' => $y['name'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => $isLast,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Create Semester Ganjil
            Semester::create([
                'academic_year_id' => $academicYear->id,
                'type' => 'ganjil',
                'start_date' => (clone $startDate),
                'end_date' => (clone $startDate)->month(12)->day(31),
                'is_active' => $isLast,
                'created_by' => $userId,
            ]);

            // Create Semester Genap
            Semester::create([
                'academic_year_id' => $academicYear->id,
                'type' => 'genap',
                'start_date' => (clone $startDate)->addYear()->month(1)->day(1),
                'end_date' => (clone $endDate),
                'is_active' => false,
                'created_by' => $userId,
            ]);
        }
    }
}
