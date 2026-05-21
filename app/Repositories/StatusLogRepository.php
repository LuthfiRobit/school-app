<?php

namespace App\Repositories;

use App\Models\StatusLog;
use App\Repositories\Interfaces\StatusLogRepositoryInterface;

class StatusLogRepository extends BaseRepository implements StatusLogRepositoryInterface
{
    public function __construct(StatusLog $model)
    {
        parent::__construct($model);
    }
}
