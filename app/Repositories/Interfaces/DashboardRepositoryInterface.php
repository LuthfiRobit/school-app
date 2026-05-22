<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface DashboardRepositoryInterface
{
    /**
     * Hitung 4 KPI utama untuk halaman dashboard admin.
     * Menggunakan satu query agregasi tunggal per metric.
     */
    public function getKpiData(): array;

    /**
     * Dapatkan statistik per jalur SPMB untuk bar chart dan tabel kuota.
     * Menggunakan GROUP BY untuk menghindari N+1 query.
     *
     * @return array<int, array{track: string, quota: int, filled: int, percentage: float, total_active: int}>
     */
    public function getTrackChartData(): array;

    /**
     * Dapatkan distribusi status enrollment untuk doughnut chart.
     *
     * @return array{labels: array<string>, data: array<int>}
     */
    public function getStatusChartData(): array;

    /**
     * Dapatkan daftar pendaftar terbaru beserta relasi yang dibutuhkan.
     */
    public function getLatestEnrollments(int $limit = 10): Collection;

    /**
     * Hitung jumlah pembayaran yang menunggu verifikasi (untuk badge notifikasi).
     */
    public function getPendingPaymentsCount(): int;
}
