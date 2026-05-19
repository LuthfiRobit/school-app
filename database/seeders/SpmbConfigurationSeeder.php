<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SpmbConfiguration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SpmbConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::whereHas('roles', function($q) {
            $q->where('name', 'Developer');
        })->first() ?? User::first();
        
        $userId = $user ? $user->id : 1;

        // Ambil 3 tahun ajaran terbaru untuk diberikan konfigurasi contoh
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->take(3)->get();

        foreach ($academicYears as $index => $ay) {
            $startDate = Carbon::parse($ay->start_date);
            
            // Simulasi periode pendaftaran: dimulai 6 bulan sebelum tahun ajaran, berakhir 1 bulan sebelum mulai
            $regStart = (clone $startDate)->subMonths(6)->day(1);
            $regEnd = (clone $startDate)->subMonths(1)->setDay(28); // Gunakan 28 agar aman di semua bulan

            // Penentuan status contoh
            // Index 0 (Terbaru) -> Active
            // Index 1 -> Closed
            // Index 2 -> Archived
            $status = 'archived';
            if ($index === 0) $status = 'active';
            if ($index === 1) $status = 'closed';

            SpmbConfiguration::updateOrCreate(
                ['academic_year_id' => $ay->id],
                [
                    'reg_start_date' => $regStart->format('Y-m-d'),
                    'reg_end_date' => $regEnd->format('Y-m-d'),
                    'total_quota' => rand(100, 250),
                    'status' => $status,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }
    }
}
