<?php

namespace App\Http\Requests\Admin\Spmb;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpmbTrackRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $trackId = $this->route('track');
        $track = \App\Models\SpmbTrack::find($trackId);
        $configurationId = $track ? $track->spmb_configuration_id : null;

        return [
            'quota' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($configurationId, $trackId) {
                    if (!$configurationId) {
                        return;
                    }
                    $config = \App\Models\SpmbConfiguration::find($configurationId);
                    if (!$config) {
                        return;
                    }

                    $otherTracksQuota = \App\Models\SpmbTrack::where('spmb_configuration_id', $configurationId)
                        ->where('id', '!=', $trackId)
                        ->sum('quota');

                    if (($otherTracksQuota + $value) > $config->total_quota) {
                        $fail("Total kuota jalur lain ({$otherTracksQuota}) ditambah kuota baru ({$value}) melebihi batas total kuota SPMB ({$config->total_quota}).");
                    }
                },
            ],
            'registration_fee' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:PRE_PAYMENT,POST_PAYMENT',
            'allow_carryover' => 'boolean',
            'status' => 'required|in:active,closed,full',
        ];
    }
}
