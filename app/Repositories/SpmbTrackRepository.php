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

    /**
     * Get active tracks with quota count and relations.
     */
    public function getActiveTracksWithQuota(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('status', 'active')
            ->withCount(['enrollments' => function ($query) {
                $query->whereNotIn('status', [
                    \App\Enums\EnrollmentStatus::REJECTED,
                    \App\Enums\EnrollmentStatus::DRAFT
                ]);
            }])
            ->with(['trackType', 'spmbConfiguration.academicYear'])
            ->get();
    }
}
