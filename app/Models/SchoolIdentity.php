<?php

namespace App\Models;

use App\Enums\Akreditasi;
use App\Enums\JenjangPendidikan;
use App\Enums\StatusKepemilikan;
use App\Enums\StatusSekolah;
use Illuminate\Database\Eloquent\Model;

class SchoolIdentity extends Model
{
    protected $fillable = [
        'school_name', 'npsn', 'education_level', 'school_status', 'ownership_status',
        'establishment_sk', 'establishment_date', 'operational_sk', 'tax_id',
        'accreditation', 'accreditation_expiry_date', 'address', 'latitude', 'longitude',
        'whatsapp', 'phone', 'email', 'website', 'headmaster_name', 'headmaster_nip',
        'treasurer_name', 'treasurer_nip', 'operator_name', 'operator_nip',
        'logo', 'stamp', 'profile_image', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'establishment_date' => 'date',
        'accreditation_expiry_date' => 'date',
        'education_level' => JenjangPendidikan::class,
        'school_status' => StatusSekolah::class,
        'ownership_status' => StatusKepemilikan::class,
        'accreditation' => Akreditasi::class,
    ];

    /**
     * Booted method untuk menangani audit log otomatis.
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang terakhir memperbarui data.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
