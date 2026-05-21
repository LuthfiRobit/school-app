<?php

namespace App\Http\Requests\Admin\Spmb;

use App\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Controlled by permission middleware in routes
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(EnrollmentStatus::class)],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
