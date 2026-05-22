<?php

namespace App\Repositories;

use App\Models\ApplicantEnrollment;
use App\Models\Payment;
use App\Models\SpmbTrack;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Helper: Dapatkan ID tahun ajaran aktif, atau fallback ke tahun terbaru.
     */
    private function resolveActiveAcademicYearId(): ?int
    {
        $activeConfig = \App\Models\SpmbConfiguration::where('status', 'active')->first();
        if ($activeConfig) {
            return $activeConfig->academic_year_id;
        }

        $lastYear = \App\Models\AcademicYear::orderBy('start_date', 'desc')->first();
        return $lastYear?->id;
    }

    /**
     * Base query untuk ApplicantEnrollment terscope ke tahun ajaran aktif
     */
    private function baseEnrollmentQuery(?int $academicYearId)
    {
        return ApplicantEnrollment::whereHas('spmbTrack.spmbConfiguration', function ($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        });
    }

    /**
     * Hitung 4 KPI utama dashboard menggunakan satu query agregasi per metric.
     */
    public function getKpiData(): array
    {
        $academicYearId = $this->resolveActiveAcademicYearId();

        // Satu query dengan conditional COUNT untuk menghindari 4 round-trip terpisah
        $aggregated = $this->baseEnrollmentQuery($academicYearId)->select([
            DB::raw("SUM(CASE WHEN status NOT IN ('draft', 'rejected') THEN 1 ELSE 0 END) AS total_pendaftar_aktif"),
            DB::raw("SUM(CASE WHEN status = 'passed' THEN 1 ELSE 0 END) AS total_passed"),
            DB::raw("SUM(CASE WHEN status = 'permanent_student' THEN 1 ELSE 0 END) AS total_permanent_student"),
        ])->first();

        $totalPenerimaan = Payment::whereHas('invoice.enrollment.spmbTrack.spmbConfiguration', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->where('status', 'confirmed')
            ->sum('confirmed_amount');

        return [
            'total_pendaftar_aktif'   => (int) ($aggregated->total_pendaftar_aktif ?? 0),
            'total_passed'            => (int) ($aggregated->total_passed ?? 0),
            'total_permanent_student' => (int) ($aggregated->total_permanent_student ?? 0),
            'total_penerimaan'        => (float) $totalPenerimaan,
        ];
    }

    /**
     * Statistik per jalur SPMB — satu query GROUP BY menggantikan loop N+1.
     */
    public function getTrackChartData(): array
    {
        $academicYearId = $this->resolveActiveAcademicYearId();

        $tracks = SpmbTrack::with('trackType')
            ->whereHas('spmbConfiguration', function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->get();

        if ($tracks->isEmpty()) {
            return [
                'chart_jalur'    => ['labels' => [], 'data' => []],
                'kuota_terisi'   => [],
            ];
        }

        // Satu query GROUP BY untuk menghitung total_active dan total_filled per track
        $stats = ApplicantEnrollment::select(
                'spmb_track_id',
                DB::raw("SUM(CASE WHEN status NOT IN ('draft', 'rejected') THEN 1 ELSE 0 END) AS total_active"),
                DB::raw("SUM(CASE WHEN status IN ('passed', 'waiting_payment_final', 'settled', 'permanent_student') THEN 1 ELSE 0 END) AS total_filled")
            )
            ->whereIn('spmb_track_id', $tracks->pluck('id'))
            ->groupBy('spmb_track_id')
            ->get()
            ->keyBy('spmb_track_id');

        $chartJalurLabels = [];
        $chartJalurData   = [];
        $kuotaTerisi      = [];

        foreach ($tracks as $track) {
            $trackName   = $track->trackType->name;
            $quota        = $track->quota ?? 0;
            $stat         = $stats->get($track->id);
            $totalActive  = (int) ($stat?->total_active ?? 0);
            $totalFilled  = (int) ($stat?->total_filled ?? 0);
            $percentage   = $quota > 0 ? round(($totalFilled / $quota) * 100, 1) : 0;

            $chartJalurLabels[] = $trackName;
            $chartJalurData[]   = $totalActive;

            $kuotaTerisi[] = [
                'track'      => $trackName,
                'quota'      => $quota,
                'filled'     => $totalFilled,
                'percentage' => $percentage,
            ];
        }

        return [
            'chart_jalur'  => [
                'labels' => $chartJalurLabels,
                'data'   => $chartJalurData,
            ],
            'kuota_terisi' => $kuotaTerisi,
        ];
    }

    /**
     * Distribusi status enrollment untuk doughnut chart.
     */
    public function getStatusChartData(): array
    {
        $academicYearId = $this->resolveActiveAcademicYearId();

        $distribution = $this->baseEnrollmentQuery($academicYearId)
            ->whereNotIn('status', ['draft'])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data   = [];

        foreach ($distribution as $item) {
            $labels[] = ucwords(str_replace('_', ' ', $item->status->value));
            $data[]   = $item->total;
        }

        return compact('labels', 'data');
    }

    /**
     * 10 pendaftar terbaru dengan eager loading relasi yang dibutuhkan view.
     */
    public function getLatestEnrollments(int $limit = 10): Collection
    {
        $academicYearId = $this->resolveActiveAcademicYearId();

        return $this->baseEnrollmentQuery($academicYearId)
            ->with(['applicant', 'spmbTrack.trackType'])
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Jumlah pembayaran pending untuk badge notifikasi admin.
     */
    public function getPendingPaymentsCount(): int
    {
        $academicYearId = $this->resolveActiveAcademicYearId();

        return Payment::whereHas('invoice.enrollment.spmbTrack.spmbConfiguration', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->where('status', 'pending')
            ->count();
    }
}
