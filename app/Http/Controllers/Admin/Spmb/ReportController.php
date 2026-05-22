<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ApplicantExport;

class ReportController extends Controller
{
    public function __construct(
        private ReportRepositoryInterface $reportRepo
    ) {}

    /**
     * Tampilkan halaman dashboard laporan.
     */
    public function index()
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $tracks = \App\Models\SpmbTrack::with('trackType')->get();
        $statuses = \App\Enums\EnrollmentStatus::cases();
        return view('admin.spmb.laporan.index', compact('academicYears', 'tracks', 'statuses'));
    }

    /**
     * Dapatkan data ringkasan laporan berdasarkan filter tahun ajaran (JSON).
     */
    public function summary(Request $request)
    {
        $academicYearId = $this->reportRepo->resolveAcademicYearId(
            $request->query('academic_year_id') ? (int) $request->query('academic_year_id') : null
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'kpi'                 => $this->reportRepo->getKpiData($academicYearId),
                'tracks'              => $this->reportRepo->getTrackData($academicYearId),
                'status_distribution' => $this->reportRepo->getStatusDistribution($academicYearId),
            ],
        ]);
    }

    /**
     * Export data pendaftar ke file Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'academic_year_id' => $request->query('academic_year_id'),
            'spmb_track_id'    => $request->query('spmb_track_id'),
            'status'           => $request->query('status'),
        ];

        $fileName = 'Laporan_SPMB_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new ApplicantExport($filters), $fileName);
    }

    /**
     * Export ringkasan laporan ke PDF.
     *
     * Menggunakan ReportRepository langsung (bukan fake HTTP request)
     * untuk mendapatkan data — sesuai prinsip Repository Pattern.
     */
    public function exportPdf(Request $request)
    {
        $academicYearId = $this->reportRepo->resolveAcademicYearId(
            $request->query('academic_year_id') ? (int) $request->query('academic_year_id') : null
        );

        $academicYear   = $academicYearId ? AcademicYear::find($academicYearId) : null;
        $schoolIdentity = DB::table('school_identities')->first();

        $pdfData = [
            'kpi'                 => $this->reportRepo->getKpiData($academicYearId),
            'tracks'              => $this->reportRepo->getTrackData($academicYearId),
            'status_distribution' => $this->reportRepo->getStatusDistribution($academicYearId),
            'academicYear'        => $academicYear,
            'schoolIdentity'      => $schoolIdentity,
            'date'                => now()->translatedFormat('d F Y'),
        ];

        $pdf      = Pdf::loadView('pdf.laporan_spmb', $pdfData);
        $fileName = 'Ringkasan_Laporan_SPMB_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
