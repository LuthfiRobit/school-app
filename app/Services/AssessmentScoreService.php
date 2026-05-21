<?php

namespace App\Services;

use App\Models\ApplicantEnrollment;
use Illuminate\Support\Facades\DB;

class AssessmentScoreService
{
    /**
     * Calculate weighted score of an enrollment.
     */
    public function calculateWeightedScore(ApplicantEnrollment $enrollment): float
    {
        // Get track assessment mappings
        $mappings = DB::table('spmb_track_assessments')
            ->where('spmb_track_id', $enrollment->spmb_track_id)
            ->get();
            
        $totalWeightedScore = 0.0;
        
        foreach ($mappings as $mapping) {
            // Find score for this mapping
            $assessment = DB::table('assessments')
                ->where('enrollment_id', $enrollment->id)
                ->where('spmb_track_assessment_id', $mapping->id)
                ->first();
                
            if ($assessment && $assessment->score) {
                // Score weighted by percentage weight (e.g. 50% = 0.5)
                $totalWeightedScore += ($assessment->score * ($mapping->weight / 100.0));
            }
        }
        
        return $totalWeightedScore;
    }

    /**
     * Check if all required assessment components have been assessed.
     */
    public function isAllComponentsAssessed(ApplicantEnrollment $enrollment): bool
    {
        $requiredCount = DB::table('spmb_track_assessments')
            ->where('spmb_track_id', $enrollment->spmb_track_id)
            ->count();
            
        $assessedCount = DB::table('assessments')
            ->where('enrollment_id', $enrollment->id)
            ->whereNotNull('score')
            ->count();
            
        return $requiredCount === $assessedCount;
    }
}
