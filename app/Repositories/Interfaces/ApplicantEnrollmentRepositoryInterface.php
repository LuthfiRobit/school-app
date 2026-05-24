<?php

namespace App\Repositories\Interfaces;

interface ApplicantEnrollmentRepositoryInterface extends EloquentRepositoryInterface
{
    public function getReRegistrationQuery(array $filters = []): \Illuminate\Database\Eloquent\Builder;
}
