<?php

namespace App\Repositories;

use App\Models\SpmbConfiguration;
use App\Repositories\Interfaces\SpmbConfigurationRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class SpmbConfigurationRepository extends BaseRepository implements SpmbConfigurationRepositoryInterface
{
    public function __construct(SpmbConfiguration $model)
    {
        parent::__construct($model);
    }

    /**
     * Get configuration by academic year ID.
     */
    public function findByAcademicYear(int $academicYearId): ?Model
    {
        return $this->model->where('academic_year_id', $academicYearId)->first();
    }
}
