<?php

namespace App\Http\Requests\Admin\Spmb;

use Illuminate\Foundation\Http\FormRequest;

class SyncSpmbTrackFormFieldRequest extends FormRequest
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
            'form_fields' => 'present|array',
            'form_fields.*.field_group' => 'required|string|max:100',
            'form_fields.*.field_name' => 'required|string|max:255',
            'form_fields.*.field_label' => 'required|string|max:255',
            'form_fields.*.field_type' => 'required|in:text,number,date,select,file,textarea,radio,checkbox',
            'form_fields.*.field_options' => 'nullable|array',
            'form_fields.*.file_types' => 'nullable|string|max:255',
            'form_fields.*.max_file_size' => 'nullable|integer|min:1',
            'form_fields.*.is_required' => 'required|boolean',
            'form_fields.*.display_order' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'form_fields.*.field_name' => 'Nama Field',
            'form_fields.*.field_label' => 'Label Field',
            'form_fields.*.field_type' => 'Tipe Field',
            'form_fields.*.field_options' => 'Pilihan Dropdown',
            'form_fields.*.is_required' => 'Wajib Diisi',
            'form_fields.*.display_order' => 'Urutan',
        ];
    }
}
