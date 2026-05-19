<?php

namespace App\Http\Requests\Admin\Spmb;

use Illuminate\Foundation\Http\FormRequest;

class SyncSpmbTrackAssessmentRequest extends FormRequest
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
            'assessments' => 'present|array',
            'assessments.*.master_assessment_type_id' => 'required|exists:master_assessment_types,id',
            'assessments.*.weight' => 'required|numeric|min:0|max:100',
            'assessments.*.passing_score' => 'nullable|numeric|min:0',
            'assessments.*.display_order' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'assessments.*.master_assessment_type_id' => 'Jenis Ujian',
            'assessments.*.weight' => 'Bobot (%)',
            'assessments.*.passing_score' => 'Nilai Kelulusan',
            'assessments.*.display_order' => 'Urutan',
        ];
    }
}
