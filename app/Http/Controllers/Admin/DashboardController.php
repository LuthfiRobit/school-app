<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\DashboardRepositoryInterface;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardRepositoryInterface $dashboardRepo
    ) {}

    public function index()
    {
        // 1. KPI Cards — satu query agregasi (bukan 4 query terpisah)
        $kpi = $this->dashboardRepo->getKpiData();

        // 2. Chart per Jalur & Tabel Kuota Terisi — satu GROUP BY query (bukan N+1)
        $trackData        = $this->dashboardRepo->getTrackChartData();
        $chartJalurData   = $trackData['chart_jalur'];
        $chartKuotaTerisi = $trackData['kuota_terisi'];

        // 3. Chart Distribusi Status
        $chartStatusData = $this->dashboardRepo->getStatusChartData();

        // 4. Tabel Pendaftar Terbaru (10 baris, eager loaded)
        $latestEnrollments = $this->dashboardRepo->getLatestEnrollments(10);

        // 5. Badge Notifikasi — pembayaran menunggu verifikasi
        $pendingPaymentsCount = $this->dashboardRepo->getPendingPaymentsCount();

        return view('admin.dashboard', [
            'totalPendaftarAktif'   => $kpi['total_pendaftar_aktif'],
            'totalPassed'           => $kpi['total_passed'],
            'totalPermanentStudent' => $kpi['total_permanent_student'],
            'totalPenerimaan'       => $kpi['total_penerimaan'],
            'chartJalurData'        => $chartJalurData,
            'chartKuotaTerisi'      => $chartKuotaTerisi,
            'chartStatusData'       => $chartStatusData,
            'latestEnrollments'     => $latestEnrollments,
            'pendingPaymentsCount'  => $pendingPaymentsCount,
        ]);
    }
}
