<?php

namespace App\Http\Requests\Admin\Spmb;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmPaymentRequest extends FormRequest
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
            'confirmed_amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
