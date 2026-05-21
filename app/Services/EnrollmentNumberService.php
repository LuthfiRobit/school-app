<?php

namespace App\Services;

use App\Models\ApplicantEnrollment;
use Illuminate\Support\Facades\DB;

class EnrollmentNumberService
{
    /**
     * Generate a unique enrollment number with format SPMB-{YEAR}-{TRACK_ID}-{SEQUENCE}
     * and assign it to the given enrollment within a DB transaction.
     *
     * Uses LOCK FOR UPDATE to prevent race conditions on the counter row.
     *
     * @param  ApplicantEnrollment  $enrollment  The enrollment to assign a number to.
     * @return string                             The generated enrollment number.
     *
     * @throws \RuntimeException if enrollment already has a number assigned.
     */
    public function generate(ApplicantEnrollment $enrollment): string
    {
        if ($enrollment->enrollment_number) {
            throw new \RuntimeException(
                "Enrollment #{$enrollment->id} sudah memiliki nomor pendaftaran: {$enrollment->enrollment_number}"
            );
        }

        $number = DB::transaction(function () use ($enrollment) {
            $year     = (int) now()->format('Y');
            $trackId  = $enrollment->spmb_track_id;

            // Lock the counter row for this track + year to prevent duplicates under concurrent requests
            $counter = DB::table('spmb_enrollment_counters')
                ->where('spmb_track_id', $trackId)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($counter) {
                $newCounter = $counter->counter + 1;
                DB::table('spmb_enrollment_counters')
                    ->where('id', $counter->id)
                    ->update([
                        'counter'    => $newCounter,
                        'updated_at' => now(),
                    ]);
            } else {
                // First registration for this track in this year
                $newCounter = 1;
                DB::table('spmb_enrollment_counters')->insert([
                    'spmb_track_id' => $trackId,
                    'year'          => $year,
                    'counter'       => $newCounter,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            $sequence = str_pad($newCounter, 4, '0', STR_PAD_LEFT);
            return "SPMB-{$year}-{$trackId}-{$sequence}";
        });

        // Persist the generated number to the enrollment
        $enrollment->enrollment_number = $number;
        $enrollment->save();

        return $number;
    }
}
