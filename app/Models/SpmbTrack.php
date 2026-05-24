<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbTrack extends Model
{
    use HasFactory;

    protected $table = 'spmb_tracks';

    protected $fillable = [
        'spmb_configuration_id',
        'master_track_type_id',
        'quota',
        'registration_fee',
        'payment_mode',
        'allow_carryover',
        'announcement_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quota' => 'integer',
        'registration_fee' => 'decimal:2',
        'allow_carryover' => 'boolean',
        'announcement_date' => 'datetime',
    ];

    /**
     * Booted method for automatic audit log.
     */
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

    /**
     * Get the SPMB configuration that owns the track.
     */
    public function spmbConfiguration(): BelongsTo
    {
        return $this->belongsTo(SpmbConfiguration::class, 'spmb_configuration_id');
    }

    /**
     * Get the master track type associated with the track.
     */
    public function trackType(): BelongsTo
    {
        return $this->belongsTo(MasterTrackType::class, 'master_track_type_id');
    }

    /**
     * Get the fees associated with the track.
     */
    public function fees(): HasMany
    {
        return $this->hasMany(SpmbTrackFee::class, 'spmb_track_id');
    }

    /**
     * Get the assessments associated with the track.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(SpmbTrackAssessment::class, 'spmb_track_id');
    }

    /**
     * Get the form fields associated with the track.
     */
    public function formFields(): HasMany
    {
        return $this->hasMany(SpmbTrackFormField::class, 'spmb_track_id');
    }

    /**
     * Get the enrollments associated with the track.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ApplicantEnrollment::class, 'spmb_track_id');
    }

    /**
     * Relationship to the user who created the record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship to the user who last updated the record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
