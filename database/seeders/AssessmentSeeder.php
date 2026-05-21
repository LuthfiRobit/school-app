<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $admin = DB::table('users')->where('username', 'developer')->first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Get Budi's enrollment
        $budiEnroll = DB::table('applicant_enrollments')
            ->join('applicants', 'applicant_enrollments.applicant_id', '=', 'applicants.id')
            ->where('applicants.full_name', 'Budi Santoso')
            ->select('applicant_enrollments.id', 'applicant_enrollments.spmb_track_id')
            ->first();

        if ($budiEnroll) {
            // Get SpmbTrackAssessment mappings for Reguler track
            $trackAssessments = DB::table('spmb_track_assessments')
                ->where('spmb_track_id', $budiEnroll->spmb_track_id)
                ->get();

            // Seed only the first component (e.g. Tes Tulis) to demonstrate partial assessment
            if ($trackAssessments->isNotEmpty()) {
                $firstAssessment = $trackAssessments->first();
                
                DB::table('assessments')->insert([
                    'enrollment_id' => $budiEnroll->id,
                    'spmb_track_assessment_id' => $firstAssessment->id,
                    'assessed_by' => $adminId,
                    'score' => 85.00,
                    'grade' => null,
                    'notes' => 'Kemampuan logika matematika sangat baik.',
                    'assessed_at' => $now->copy()->subDay(),
                    'created_at' => $now->copy()->subDay(),
                    'updated_at' => $now->copy()->subDay(),
                ]);
            }
        }
    }
}
