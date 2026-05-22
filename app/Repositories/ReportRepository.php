<?php

namespace App\Repositories;

use App\Models\ApplicantEnrollment;
use App\Models\AcademicYear;
use App\Models\SpmbConfiguration;
use App\Models\SpmbTrack;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{
    /**
     * Resolve academic year ID dari request parameter.
     * Fallback hierarchy:
     *   1. ID yang diminta (jika ada)
     *   2. Konfigurasi SPMB yang aktif
     *   3. Tahun ajaran terbaru berdasarkan start_date
     */
    public function resolveAcademicYearId(?int $requestedId): ?int
    {
        if ($requestedId) {
            return $requestedId;
        }

        $activeConfig = SpmbConfiguration::where('status', 'active')->first();
        if ($activeConfig) {
            return $activeConfig->academic_year_id;
        }

        $lastYear = AcademicYear::orderBy('start_date', 'desc')->first();
        return $lastYear?->id;
    }

    /**
     * Hitung 4 KPI utama untuk tahun ajaran tertentu.
     */
    public function getKpiData(?int $academicYearId): array
    {
        $baseQuery = $this->baseEnrollmentQuery($academicYearId);

        $totalPendaftar = (clone $baseQuery)
            ->whereNotIn('status', ['draft'])
            ->count();

        $totalLulus = (clone $baseQuery)
            ->whereIn('status', ['passed', 'waiting_payment_final', 'settled', 'permanent_student'])
            ->count();

        $totalSiswaTetap = (clone $baseQuery)
            ->where('status', 'permanent_student')
            ->count();

        $totalPenerimaan = DB::table('payments')
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('applicant_enrollments', 'invoices.enrollment_id', '=', 'applicant_enrollments.id')
            ->join('spmb_tracks', 'applicant_enrollments.spmb_track_id', '=', 'spmb_tracks.id')
            ->join('spmb_configurations', 'spmb_tracks.spmb_configuration_id', '=', 'spmb_configurations.id')
            ->where('spmb_configurations.academic_year_id', $academicYearId)
            ->where('payments.status', 'confirmed')
            ->sum('payments.confirmed_amount');

        return [
            'total_pendaftar'   => $totalPendaftar,
            'total_lulus'       => $totalLulus,
            'total_siswa_tetap' => $totalSiswaTetap,
            'total_penerimaan'  => $totalPenerimaan,
        ];
    }

    /**
     * Dapatkan statistik per jalur SPMB (untuk bar chart & tabel ringkasan).
     */
    public function getTrackData(?int $academicYearId): array
    {
        $tracks = SpmbTrack::with('trackType')
            ->whereHas('spmbConfiguration', fn($q) => $q->where('academic_year_id', $academicYearId))
            ->get();

        // Hitung statistik enrollment per track sekaligus (hindari N+1)
        $stats = ApplicantEnrollment::select(
                'spmb_track_id',
                DB::raw("SUM(CASE WHEN status NOT IN ('draft', 'rejected') THEN 1 ELSE 0 END) AS total_active"),
                DB::raw("SUM(CASE WHEN status IN ('passed', 'waiting_payment_final', 'settled', 'permanent_student') THEN 1 ELSE 0 END) AS total_passed")
            )
            ->whereIn('spmb_track_id', $tracks->pluck('id'))
            ->groupBy('spmb_track_id')
            ->get()
            ->keyBy('spmb_track_id');

        $trackData = [];
        foreach ($tracks as $track) {
            $stat        = $stats->get($track->id);
            $totalActive = $stat?->total_active ?? 0;
            $totalPassed = $stat?->total_passed ?? 0;
            $quota       = $track->quota ?? 0;

            $trackData[] = [
                'id'             => $track->id,
                'name'           => $track->trackType->name,
                'quota'          => $quota,
                'total_active'   => $totalActive,
                'total_passed'   => $totalPassed,
                'fill_percentage' => $quota > 0
                    ? round(($totalPassed / $quota) * 100, 1)
                    : 0,
            ];
        }

        return $trackData;
    }

    /**
     * Dapatkan distribusi status enrollment (untuk doughnut chart).
     */
    public function getStatusDistribution(?int $academicYearId): array
    {
        return $this->baseEnrollmentQuery($academicYearId)
            ->whereNotIn('status', ['draft'])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [
                'status' => $item->status->value,
                'label'  => $item->status->label(),
                'total'  => $item->total,
                'color'  => $this->getStatusColor($item->status->value),
            ])
            ->toArray();
    }

    // -----------------------------------------------------------------------
    // Private Helpers
    // -----------------------------------------------------------------------

    /**
     * Query dasar enrollment yang di-scope ke tahun ajaran tertentu.
     */
    private function baseEnrollmentQuery(?int $academicYearId)
    {
        return ApplicantEnrollment::whereHas(
            'spmbTrack.spmbConfiguration',
            fn($q) => $q->where('academic_year_id', $academicYearId)
        );
    }

    /**
     * Mapping warna hex untuk setiap status enrollment (digunakan di chart).
     */
    private function getStatusColor(string $status): string
    {
        $colors = [
            'registered'            => '#E58A00',
            'waiting_payment_reg'   => '#E58A00',
            'verified_reg'          => '#4680FF',
            'in_review'             => '#4680FF',
            'passed'                => '#2CA87F',
            'waiting_list'          => '#E58A00',
            'rejected'              => '#DC2626',
            'waiting_payment_final' => '#4680FF',
            'settled'               => '#2CA87F',
            'permanent_student'     => '#2CA87F',
        ];

        return $colors[$status] ?? '#6B7280';
    }
}
