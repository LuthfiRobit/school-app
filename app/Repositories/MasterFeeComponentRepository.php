<?php

namespace App\Repositories;

use App\Models\MasterFeeComponent;
use App\Repositories\Interfaces\MasterFeeComponentRepositoryInterface;

class MasterFeeComponentRepository extends BaseRepository implements MasterFeeComponentRepositoryInterface
{
    public function __construct(MasterFeeComponent $model)
    {
        parent::__construct($model);
    }
}
