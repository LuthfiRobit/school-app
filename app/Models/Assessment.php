<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'spmb_track_assessment_id',
        'assessed_by',
        'score',
        'grade',
        'notes',
        'assessed_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'assessed_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ApplicantEnrollment::class, 'enrollment_id');
    }

    public function trackAssessment(): BelongsTo
    {
        return $this->belongsTo(SpmbTrackAssessment::class, 'spmb_track_assessment_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
}
