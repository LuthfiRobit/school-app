<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusLog extends Model
{
    use HasFactory;

    protected $table = 'status_logs';

    // Disable default timestamps because we use changed_at
    public $timestamps = false;

    protected $fillable = [
        'enrollment_id',
        'changed_by',
        'from_status',
        'to_status',
        'reason',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->changed_at) {
                $model->changed_at = now();
            }
        });

        static::updating(function ($model) {
            throw new \Exception("Cannot update an append-only log record.");
        });

        static::deleting(function ($model) {
            throw new \Exception("Cannot delete an append-only log record.");
        });
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ApplicantEnrollment::class, 'enrollment_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
