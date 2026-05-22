<?php

namespace App\Repositories\Interfaces;

interface ReportRepositoryInterface
{
    /**
     * Resolve academic year ID dari request.
     * Fallback: konfigurasi aktif → tahun ajaran terbaru.
     */
    public function resolveAcademicYearId(?int $requestedId): ?int;

    /**
     * Hitung KPI utama (total pendaftar, lulus, siswa tetap, penerimaan).
     */
    public function getKpiData(?int $academicYearId): array;

    /**
     * Dapatkan data per jalur (kuota, pendaftar aktif, lulus, persentase).
     */
    public function getTrackData(?int $academicYearId): array;

    /**
     * Dapatkan distribusi status pendaftar (untuk pie/doughnut chart).
     */
    public function getStatusDistribution(?int $academicYearId): array;
}
