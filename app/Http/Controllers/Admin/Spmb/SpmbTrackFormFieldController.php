<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Spmb\SyncSpmbTrackFormFieldRequest;
use App\Models\SpmbTrack;
use App\Models\SpmbTrackFormField;
use App\Repositories\Interfaces\SpmbTrackFormFieldRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SpmbTrackFormFieldController extends Controller
{
    public function __construct(
        protected SpmbTrackFormFieldRepositoryInterface $formFieldRepository,
        protected SpmbTrackRepositoryInterface $trackRepository
    ) {}

    /**
     * Get all form fields mapped to a specific track.
     */
    public function index($configurationId, $trackId)
    {
        $track = $this->trackRepository->find($trackId);
        if (!$track || $track->spmb_configuration_id != $configurationId) {
            return response()->json(['message' => 'Jalur tidak ditemukan'], 404);
        }

        $fields = $this->formFieldRepository->all()->where('spmb_track_id', $trackId)->sortBy('display_order')->values();
        
        return response()->json($fields);
    }

    /**
     * Bulk Sync form fields for a specific track.
     */
    public function sync(SyncSpmbTrackFormFieldRequest $request, $configurationId, $trackId)
    {
        $track = $this->trackRepository->find($trackId);
        if (!$track || $track->spmb_configuration_id != $configurationId) {
            return response()->json(['message' => 'Jalur tidak ditemukan'], 404);
        }

        $fieldsData = $request->validated('form_fields', []);

        DB::beginTransaction();
        try {
            // Get existing IDs for cleanup
            $existingFieldIds = SpmbTrackFormField::where('spmb_track_id', $trackId)->pluck('id')->toArray();
            
            $syncedIds = [];

            foreach ($fieldsData as $data) {
                // Konversi options jika null atau array kosong
                $options = null;
                if (in_array($data['field_type'], ['select', 'radio', 'checkbox']) && !empty($data['field_options'])) {
                    $options = is_array($data['field_options']) ? $data['field_options'] : json_decode($data['field_options'], true);
                }

                $mapped = SpmbTrackFormField::updateOrCreate(
                    [
                        'spmb_track_id' => $trackId,
                        'field_name' => $data['field_name'],
                    ],
                    [
                        'field_group' => $data['field_group'] ?? 'data_pribadi',
                        'field_label' => $data['field_label'],
                        'field_type' => $data['field_type'],
                        'field_options' => $options,
                        'file_types' => $data['file_types'] ?? null,
                        'max_file_size' => $data['max_file_size'] ?? null,
                        'is_required' => $data['is_required'],
                        'display_order' => $data['display_order'] ?? 0,
                        'updated_by' => auth()->id(),
                    ]
                );

                $syncedIds[] = $mapped->id;
            }

            // Remove form fields not in input list
            $idsToDelete = array_diff($existingFieldIds, $syncedIds);
            if (!empty($idsToDelete)) {
                SpmbTrackFormField::whereIn('id', $idsToDelete)->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Form fields berhasil disimpan.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() == 23000) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan: Terdapat duplikasi nama field pada jalur ini.'
                ], 422);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
