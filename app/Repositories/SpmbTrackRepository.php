<?php

namespace App\Repositories;

use App\Models\SpmbTrack;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;

class SpmbTrackRepository extends BaseRepository implements SpmbTrackRepositoryInterface
{
    public function __construct(SpmbTrack $model)
    {
        parent::__construct($model);
    }
}
