<?php

namespace App\Http\Requests\Admin\Spmb;

use Illuminate\Foundation\Http\FormRequest;

class UpsertAssessmentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'spmb_track_assessment_id' => ['required', 'integer', 'exists:spmb_track_assessments,id'],
            'score'                    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'grade'                    => ['nullable', 'in:pass,fail'],
            'notes'                    => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Human-readable attribute names for validation messages.
     */
    public function attributes(): array
    {
        return [
            'spmb_track_assessment_id' => 'Komponen penilaian',
            'score'                    => 'Nilai',
            'grade'                    => 'Grade',
            'notes'                    => 'Catatan',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'score.min' => 'Nilai minimal adalah 0.',
            'score.max' => 'Nilai maksimal adalah 100.',
            'grade.in'  => 'Grade harus salah satu dari: pass, fail.',
        ];
    }
}
