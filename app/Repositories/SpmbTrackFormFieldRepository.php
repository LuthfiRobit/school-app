<?php

namespace App\Repositories;

use App\Models\SpmbTrackFormField;
use App\Repositories\Interfaces\SpmbTrackFormFieldRepositoryInterface;

class SpmbTrackFormFieldRepository extends BaseRepository implements SpmbTrackFormFieldRepositoryInterface
{
    public function __construct(SpmbTrackFormField $model)
    {
        parent::__construct($model);
    }
}
