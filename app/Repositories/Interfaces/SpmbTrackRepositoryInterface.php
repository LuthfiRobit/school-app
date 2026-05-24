<?php

namespace App\Repositories\Interfaces;

interface SpmbTrackRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get active tracks with quota count and relations.
     */
    public function getActiveTracksWithQuota(): \Illuminate\Database\Eloquent\Collection;
}
