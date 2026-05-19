<?php

namespace App\Http\Requests\Admin\Spmb;

use Illuminate\Foundation\Http\FormRequest;

class SyncSpmbTrackFeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Dikelola via middleware di routes
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fees' => 'present|array',
            'fees.*.master_fee_component_id' => 'required|exists:master_fee_components,id',
            'fees.*.amount' => 'required|numeric|min:0',
            'fees.*.category' => 'required|in:registration,re_registration',
            'fees.*.display_order' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'fees.*.master_fee_component_id' => 'Komponen Biaya',
            'fees.*.amount' => 'Nominal Biaya',
            'fees.*.category' => 'Kategori Biaya',
            'fees.*.display_order' => 'Urutan',
        ];
    }
}
