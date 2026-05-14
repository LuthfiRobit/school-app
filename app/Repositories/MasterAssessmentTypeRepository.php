<?php

namespace App\Repositories;

use App\Models\MasterAssessmentType;
use App\Repositories\Interfaces\MasterAssessmentTypeRepositoryInterface;

class MasterAssessmentTypeRepository extends BaseRepository implements MasterAssessmentTypeRepositoryInterface
{
    public function __construct(MasterAssessmentType $model)
    {
        parent::__construct($model);
    }
}
