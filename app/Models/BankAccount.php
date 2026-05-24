<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'branch',
        'description',
        'logo_path',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Booted method untuk audit log otomatis.
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
     * Relasi ke User yang membuat data.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang terakhir memperbarui data.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
