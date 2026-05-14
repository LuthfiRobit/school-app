<?php

namespace App\Repositories\Interfaces;

use App\Models\SchoolIdentity;

interface SchoolIdentityRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get the first school identity record.
     */
    public function first(): ?SchoolIdentity;

    /**
     * Update or create the school identity record.
     */
    public function updateOrCreate(array $data): SchoolIdentity;
}
