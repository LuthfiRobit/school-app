<?php

namespace App\Repositories;

use App\Models\SpmbTrackFee;
use App\Repositories\Interfaces\SpmbTrackFeeRepositoryInterface;

class SpmbTrackFeeRepository extends BaseRepository implements SpmbTrackFeeRepositoryInterface
{
    public function __construct(SpmbTrackFee $model)
    {
        parent::__construct($model);
    }
}
