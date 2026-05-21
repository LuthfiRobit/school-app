<?php

namespace App\Http\Requests\Admin\Spmb;

use Illuminate\Foundation\Http\FormRequest;

class BulkDecisionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization is handled by the permission middleware in routes.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Validates a batch of enrollment IDs and a single target decision status.
     */
    public function rules(): array
    {
        return [
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['required', 'integer', 'exists:applicant_enrollments,id'],
            'status' => ['required', 'in:passed,waiting_list,rejected'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Human-readable attribute names for validation messages.
     */
    public function attributes(): array
    {
        return [
            'ids'    => 'Daftar pendaftar',
            'ids.*'  => 'ID pendaftar',
            'status' => 'Status kelulusan',
            'reason' => 'Alasan/Catatan',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'ids.required'    => 'Pilih minimal satu pendaftar.',
            'ids.min'         => 'Pilih minimal satu pendaftar.',
            'ids.*.exists'    => 'Salah satu pendaftar yang dipilih tidak valid.',
            'status.required' => 'Status kelulusan wajib dipilih.',
            'status.in'       => 'Status kelulusan harus salah satu dari: Lulus, Cadangan, atau Tidak Lulus.',
        ];
    }
}
