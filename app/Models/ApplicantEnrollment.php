<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'spmb_track_id',
        'enrollment_number',
        'status',
        'waitlist_order',
        'announcement_visible_at',
        'enrolled_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => EnrollmentStatus::class,
        'waitlist_order' => 'integer',
        'announcement_visible_at' => 'datetime',
        'enrolled_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->created_by) {
                $model->created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function spmbTrack(): BelongsTo
    {
        return $this->belongsTo(SpmbTrack::class, 'spmb_track_id');
    }

    public function formData(): HasMany
    {
        return $this->hasMany(ApplicantFormData::class, 'enrollment_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'enrollment_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'enrollment_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'enrollment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
