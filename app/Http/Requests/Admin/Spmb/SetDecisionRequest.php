<?php

namespace App\Http\Requests\Admin\Spmb;

use App\Enums\EnrollmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SetDecisionRequest extends FormRequest
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
     * Only PASSED, WAITING_LIST, REJECTED are valid outcomes for kelulusan.
     */
    public function rules(): array
    {
        return [
            'status'         => ['required', 'in:passed,waiting_list,rejected'],
            'waitlist_order' => ['required_if:status,waiting_list', 'nullable', 'integer', 'min:1'],
            'reason'         => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Human-readable attribute names for validation messages.
     */
    public function attributes(): array
    {
        return [
            'status'         => 'Status kelulusan',
            'waitlist_order' => 'Nomor urut cadangan',
            'reason'         => 'Alasan/Catatan',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'status.in'                    => 'Status kelulusan harus salah satu dari: Lulus, Cadangan, atau Tidak Lulus.',
            'waitlist_order.required_if'   => 'Nomor urut cadangan wajib diisi jika status adalah Cadangan (Waiting List).',
            'waitlist_order.min'           => 'Nomor urut cadangan minimal 1.',
        ];
    }
}
