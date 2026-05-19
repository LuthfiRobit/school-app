<?php

namespace App\Services;

use App\Models\SpmbConfiguration;
use App\Models\SpmbTrack;
use Illuminate\Support\Facades\DB;

class SpmbConfigurationCloneService
{
    /**
     * Clone all configurations from a source academic year config to a target config.
     *
     * @param int $sourceConfigurationId
     * @param int $targetConfigurationId
     * @return bool
     * @throws \Exception
     */
    public function clone(int $sourceConfigurationId, int $targetConfigurationId): bool
    {
        $sourceConfig = SpmbConfiguration::with([
            'tracks.fees',
            'tracks.assessments',
            'tracks.formFields'
        ])->find($sourceConfigurationId);

        if (!$sourceConfig) {
            throw new \Exception("Source SPMB Configuration not found.");
        }

        $targetConfig = SpmbConfiguration::find($targetConfigurationId);

        if (!$targetConfig) {
            throw new \Exception("Target SPMB Configuration not found.");
        }

        DB::beginTransaction();

        try {
            foreach ($sourceConfig->tracks as $sourceTrack) {
                // 1. Clone Track
                $newTrack = SpmbTrack::create([
                    'spmb_configuration_id' => $targetConfig->id,
                    'master_track_type_id'  => $sourceTrack->master_track_type_id,
                    'quota'                 => $sourceTrack->quota,
                    'registration_fee'      => $sourceTrack->registration_fee,
                    'payment_mode'          => $sourceTrack->payment_mode,
                    'allow_carryover'       => $sourceTrack->allow_carryover,
                    'status'                => $sourceTrack->status,
                ]);

                // 2. Clone Track Fees
                foreach ($sourceTrack->fees as $sourceFee) {
                    $newTrack->fees()->create([
                        'master_fee_component_id' => $sourceFee->master_fee_component_id,
                        'amount'                  => $sourceFee->amount,
                        'category'                => $sourceFee->category,
                        'display_order'           => $sourceFee->display_order,
                    ]);
                }

                // 3. Clone Track Assessments
                foreach ($sourceTrack->assessments as $sourceAssessment) {
                    $newTrack->assessments()->create([
                        'master_assessment_type_id' => $sourceAssessment->master_assessment_type_id,
                        'weight'                    => $sourceAssessment->weight,
                        'passing_score'             => $sourceAssessment->passing_score,
                        'display_order'             => $sourceAssessment->display_order,
                    ]);
                }

                // 4. Clone Track Form Fields
                foreach ($sourceTrack->formFields as $sourceField) {
                    $newTrack->formFields()->create([
                        'field_name'    => $sourceField->field_name,
                        'field_label'   => $sourceField->field_label,
                        'field_type'    => $sourceField->field_type,
                        'field_options' => $sourceField->field_options,
                        'is_required'   => $sourceField->is_required,
                        'display_order' => $sourceField->display_order,
                    ]);
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
