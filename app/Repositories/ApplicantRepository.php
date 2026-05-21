<?php

namespace App\Repositories;

use App\Models\Applicant;
use App\Repositories\Interfaces\ApplicantRepositoryInterface;

class ApplicantRepository extends BaseRepository implements ApplicantRepositoryInterface
{
    public function __construct(Applicant $model)
    {
        parent::__construct($model);
    }
}
