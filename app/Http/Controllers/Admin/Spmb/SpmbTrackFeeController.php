<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Spmb\SyncSpmbTrackFeeRequest;
use App\Models\SpmbTrack;
use App\Models\SpmbTrackFee;
use App\Repositories\Interfaces\SpmbTrackFeeRepositoryInterface;
use App\Repositories\Interfaces\SpmbTrackRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SpmbTrackFeeController extends Controller
{
    public function __construct(
        protected SpmbTrackFeeRepositoryInterface $feeRepository,
        protected SpmbTrackRepositoryInterface $trackRepository
    ) {}

    /**
     * Get all fees mapped to a specific track.
     */
    public function index($configurationId, $trackId)
    {
        // Pastikan track-nya ada
        $track = $this->trackRepository->find($trackId);
        if (!$track || $track->spmb_configuration_id != $configurationId) {
            return response()->json(['message' => 'Jalur tidak ditemukan'], 404);
        }

        $fees = $this->feeRepository->all(['*'], ['feeComponent'])->where('spmb_track_id', $trackId)->values();
        
        return response()->json($fees);
    }

    /**
     * Get all master fee components available.
     */
    public function masterData($configurationId, $trackId)
    {
        $masters = \App\Models\MasterFeeComponent::where('is_active', true)->get();
        return response()->json($masters);
    }

    /**
     * Bulk Sync fees for a specific track.
     */
    public function sync(SyncSpmbTrackFeeRequest $request, $configurationId, $trackId)
    {
        $track = $this->trackRepository->find($trackId);
        if (!$track || $track->spmb_configuration_id != $configurationId) {
            return response()->json(['message' => 'Jalur tidak ditemukan'], 404);
        }

        $feesData = $request->validated('fees', []);

        DB::beginTransaction();
        try {
            // Dapatkan ID yang sudah ada sebelum sync
            $existingFeeIds = SpmbTrackFee::where('spmb_track_id', $trackId)->pluck('id')->toArray();
            
            $syncedIds = [];

            // Proses data dari frontend
            foreach ($feesData as $feeData) {
                // Periksa apakah master fee component duplicate dalam array input (prevent duplikat di DB)
                // Ini ditangani DB Unique Key, tapi kita tangkap jika bisa
                
                $mappedFee = SpmbTrackFee::updateOrCreate(
                    [
                        'spmb_track_id' => $trackId,
                        'master_fee_component_id' => $feeData['master_fee_component_id'],
                    ],
                    [
                        'amount' => $feeData['amount'],
                        'category' => $feeData['category'],
                        'display_order' => $feeData['display_order'] ?? 0,
                        'updated_by' => auth()->id(),
                    ]
                );

                $syncedIds[] = $mappedFee->id;
            }

            // Hapus fee yang tidak ada di list input
            $idsToDelete = array_diff($existingFeeIds, $syncedIds);
            if (!empty($idsToDelete)) {
                foreach ($idsToDelete as $idToDel) {
                    $this->feeRepository->delete($idToDel);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Komponen biaya berhasil disimpan.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            // Handle unique constraint violation
            if ($e->getCode() == 23000) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan: Terdapat duplikasi komponen biaya pada jalur ini.'
                ], 422);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
