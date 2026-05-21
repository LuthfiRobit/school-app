<?php

namespace App\Repositories;

use App\Models\ApplicantEnrollment;
use App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface;

class ApplicantEnrollmentRepository extends BaseRepository implements ApplicantEnrollmentRepositoryInterface
{
    public function __construct(ApplicantEnrollment $model)
    {
        parent::__construct($model);
    }
}
