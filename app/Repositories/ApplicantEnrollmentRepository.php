<?php

namespace App\Repositories;

use App\Models\ApplicantEnrollment;
use App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface;

class ApplicantEnrollmentRepository extends BaseRepository implements ApplicantEnrollmentRepositoryInterface
{
    public function __construct(ApplicantEnrollment $model)
    {
        parent::__construct($model);
    }

    public function getReRegistrationQuery(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->model->newQuery()
            ->with(['applicant', 'spmbTrack.trackType', 'spmbTrack.spmbConfiguration.academicYear', 'invoices' => function($q) {
                $q->where('category', 're_registration');
            }])
            ->whereIn('status', [
                \App\Enums\EnrollmentStatus::PASSED,
                \App\Enums\EnrollmentStatus::WAITING_PAYMENT_FINAL,
                \App\Enums\EnrollmentStatus::SETTLED,
                \App\Enums\EnrollmentStatus::PERMANENT_STUDENT
            ]);

        // Filter academic_year_id
        if (!empty($filters['academic_year_id'])) {
            $query->whereHas('spmbTrack.spmbConfiguration', function ($q) use ($filters) {
                $q->where('academic_year_id', $filters['academic_year_id']);
            });
        }

        // Filter spmb_track_id
        if (!empty($filters['spmb_track_id'])) {
            $query->where('spmb_track_id', $filters['spmb_track_id']);
        }

        // Filter status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }
}
