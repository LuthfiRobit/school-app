<?php

namespace App\Repositories;

use App\Models\SpmbTrackAssessment;
use App\Repositories\Interfaces\SpmbTrackAssessmentRepositoryInterface;

class SpmbTrackAssessmentRepository extends BaseRepository implements SpmbTrackAssessmentRepositoryInterface
{
    public function __construct(SpmbTrackAssessment $model)
    {
        parent::__construct($model);
    }
}
