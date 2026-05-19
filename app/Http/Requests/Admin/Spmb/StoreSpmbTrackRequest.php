<?php

namespace App\Http\Requests\Admin\Spmb;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpmbTrackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // RBAC middleware handles authorization in route
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $configurationId = $this->route('configuration');

        return [
            'master_track_type_id' => [
                'required',
                'exists:master_track_types,id',
                \Illuminate\Validation\Rule::unique('spmb_tracks')->where(fn($query) => $query->where('spmb_configuration_id', $configurationId)),
            ],
            'quota' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($configurationId) {
                    $config = \App\Models\SpmbConfiguration::find($configurationId);
                    if (!$config) {
                        return;
                    }

                    $currentTotalQuota = \App\Models\SpmbTrack::where('spmb_configuration_id', $configurationId)->sum('quota');
                    if (($currentTotalQuota + $value) > $config->total_quota) {
                        $fail("Total kuota jalur saat ini ({$currentTotalQuota}) ditambah kuota baru ({$value}) melebihi batas total kuota SPMB ({$config->total_quota}).");
                    }
                },
            ],
            'registration_fee' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:PRE_PAYMENT,POST_PAYMENT',
            'allow_carryover' => 'boolean',
            'status' => 'required|in:active,closed,full',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'master_track_type_id.unique' => 'Jalur ini sudah terdaftar pada tahun ajaran ini.',
        ];
    }
}
