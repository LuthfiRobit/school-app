<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Spmb\SyncSpmbTrackAssessmentRequest;
use App\Models\SpmbTrack;
use App\Models\SpmbTrackAssessment;
use App\Repositories\Interfaces\SpmbTrackAssessmentRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SpmbTrackAssessmentController extends Controller
{
    public function __construct(
        protected SpmbTrackAssessmentRepositoryInterface $assessmentRepository,
        protected SpmbTrackRepositoryInterface $trackRepository
    ) {}

    /**
     * Get all assessments mapped to a specific track.
     */
    public function index($configurationId, $trackId)
    {
        $track = $this->trackRepository->find($trackId);
        if (!$track || $track->spmb_configuration_id != $configurationId) {
            return response()->json(['message' => 'Jalur tidak ditemukan'], 404);
        }

        $assessments = $this->assessmentRepository->all(['*'], ['assessmentType'])->where('spmb_track_id', $trackId)->values();
        
        return response()->json($assessments);
    }

    /**
     * Get all master assessment types available.
     */
    public function masterData($configurationId, $trackId)
    {
        $masters = \App\Models\MasterAssessmentType::where('is_active', true)->get();
        return response()->json($masters);
    }

    /**
     * Bulk Sync assessments for a specific track.
     */
    public function sync(SyncSpmbTrackAssessmentRequest $request, $configurationId, $trackId)
    {
        $track = $this->trackRepository->find($trackId);
        if (!$track || $track->spmb_configuration_id != $configurationId) {
            return response()->json(['message' => 'Jalur tidak ditemukan'], 404);
        }

        $assessmentsData = $request->validated('assessments', []);

        DB::beginTransaction();
        try {
            // Get existing IDs for cleanup
            $existingAssessmentIds = SpmbTrackAssessment::where('spmb_track_id', $trackId)->pluck('id')->toArray();
            
            $syncedIds = [];

            foreach ($assessmentsData as $data) {
                $mapped = SpmbTrackAssessment::updateOrCreate(
                    [
                        'spmb_track_id' => $trackId,
                        'master_assessment_type_id' => $data['master_assessment_type_id'],
                    ],
                    [
                        'weight' => $data['weight'],
                        'passing_score' => $data['passing_score'],
                        'display_order' => $data['display_order'] ?? 0,
                        'updated_by' => auth()->id(),
                    ]
                );

                $syncedIds[] = $mapped->id;
            }

            // Remove assessments not in input list
            $idsToDelete = array_diff($existingAssessmentIds, $syncedIds);
            if (!empty($idsToDelete)) {
                SpmbTrackAssessment::whereIn('id', $idsToDelete)->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Jenis ujian berhasil disimpan.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            if ($e->getCode() == 23000) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan: Terdapat duplikasi jenis ujian pada jalur ini.'
                ], 422);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
