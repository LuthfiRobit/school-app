<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbConfiguration extends Model
{
    use HasFactory;

    protected $table = 'spmb_configurations';

    protected $fillable = [
        'academic_year_id',
        'reg_start_date',
        'reg_end_date',
        'total_quota',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'reg_start_date' => 'date',
        'reg_end_date' => 'date',
        'total_quota' => 'integer',
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
     * Relationship to the Academic Year.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
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
