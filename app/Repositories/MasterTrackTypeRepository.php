<?php

namespace App\Repositories;

use App\Models\MasterTrackType;
use App\Repositories\Interfaces\MasterTrackTypeRepositoryInterface;

class MasterTrackTypeRepository extends BaseRepository implements MasterTrackTypeRepositoryInterface
{
    public function __construct(MasterTrackType $model)
    {
        parent::__construct($model);
    }
}
