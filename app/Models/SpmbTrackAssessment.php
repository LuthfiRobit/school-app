<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbTrackAssessment extends Model
{
    use HasFactory;

    protected $table = 'spmb_track_assessments';

    protected $fillable = [
        'spmb_track_id',
        'master_assessment_type_id',
        'weight',
        'passing_score',
        'display_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'display_order' => 'integer',
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
     * Get the SPMB track that owns the assessment mapping.
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(SpmbTrack::class, 'spmb_track_id');
    }

    /**
     * Get the master assessment type associated with this mapping.
     */
    public function assessmentType(): BelongsTo
    {
        return $this->belongsTo(MasterAssessmentType::class, 'master_assessment_type_id');
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
