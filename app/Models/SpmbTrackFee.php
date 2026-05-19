<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbTrackFee extends Model
{
    use HasFactory;

    protected $table = 'spmb_track_fees';

    protected $fillable = [
        'spmb_track_id',
        'master_fee_component_id',
        'amount',
        'category',
        'display_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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
     * Get the SPMB track that owns the fee mapping.
     */
    public function track(): BelongsTo
    {
        return $this->belongsTo(SpmbTrack::class, 'spmb_track_id');
    }

    /**
     * Get the master fee component associated with this mapping.
     */
    public function feeComponent(): BelongsTo
    {
        return $this->belongsTo(MasterFeeComponent::class, 'master_fee_component_id');
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
