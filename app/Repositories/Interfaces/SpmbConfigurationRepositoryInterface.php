<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface SpmbConfigurationRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get configuration by academic year ID.
     */
    public function findByAcademicYear(int $academicYearId): ?Model;

    /**
     * Get the active SPMB configuration.
     */
    public function getActiveConfiguration(): ?Model;
}
