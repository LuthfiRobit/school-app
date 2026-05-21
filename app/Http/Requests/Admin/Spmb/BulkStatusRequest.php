<?php

namespace App\Http\Requests\Admin\Spmb;

use App\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class BulkStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:applicant_enrollments,id'],
            'status' => ['required', new Enum(EnrollmentStatus::class)],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
