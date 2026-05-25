<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicantFormData extends Model
{
    use HasFactory;

    protected $table = 'applicant_form_data';

    protected $fillable = [
        'enrollment_id',
        'field_id',
        'value',
        'is_valid',
        'validation_note',
    ];

    protected $casts = [
        'is_valid' => 'boolean',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ApplicantEnrollment::class, 'enrollment_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(SpmbTrackFormField::class, 'field_id');
    }
}
