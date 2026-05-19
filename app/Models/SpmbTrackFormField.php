<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbTrackFormField extends Model
{
    use HasFactory;

    protected $table = 'spmb_track_form_fields';

    protected $fillable = [
        'spmb_track_id',
        'field_group',
        'field_name',
        'field_label',
        'field_type',
        'field_options',
        'file_types',
        'max_file_size',
        'is_required',
        'display_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'field_options' => 'array',
        'max_file_size' => 'integer',
        'is_required' => 'boolean',
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
     * Get the SPMB track that owns the form field mapping.
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(SpmbTrack::class, 'spmb_track_id');
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
