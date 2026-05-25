<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentProofRequest extends FormRequest
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
            'payment_proof' => ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:2048'],
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'payment_proof' => 'Bukti Pembayaran',
            'amount' => 'Nominal Transfer',
        ];
    }
}
