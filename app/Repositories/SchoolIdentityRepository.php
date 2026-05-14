<?php

namespace App\Repositories;

use App\Models\SchoolIdentity;
use App\Repositories\Interfaces\SchoolIdentityRepositoryInterface;

class SchoolIdentityRepository extends BaseRepository implements SchoolIdentityRepositoryInterface
{
    public function __construct(SchoolIdentity $model)
    {
        parent::__construct($model);
    }

    public function first(): ?SchoolIdentity
    {
        return $this->model->first();
    }

    public function updateOrCreate(array $data): SchoolIdentity
    {
        return $this->model->updateOrCreate(['id' => 1], $data);
    }
}
