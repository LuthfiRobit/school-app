<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Models\ApplicantEnrollment;
use App\Enums\EnrollmentStatus;
use App\Services\StateMachineService;
use App\Services\PdfGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class ReRegistrationController extends Controller
{
    public function __construct(
        protected StateMachineService $stateMachineService,
        protected PdfGeneratorService $pdfGeneratorService
    ) {}

    public function index()
    {
        $academicYears = \App\Models\AcademicYear::all();
        $tracks = \App\Models\SpmbTrack::with(['trackType', 'spmbConfiguration.academicYear'])->get();
        
        $statuses = [
            EnrollmentStatus::PASSED,
            EnrollmentStatus::WAITING_PAYMENT_FINAL,
            EnrollmentStatus::SETTLED,
            EnrollmentStatus::PERMANENT_STUDENT
        ];

        return view('admin.spmb.daftar_ulang.index', compact('academicYears', 'tracks', 'statuses'));
    }

    public function getData(Request $request)
    {
        $query = ApplicantEnrollment::with(['applicant', 'spmbTrack.trackType', 'spmbTrack.spmbConfiguration.academicYear', 'invoices' => function($q) {
                $q->where('category', 're_registration');
            }])
            ->whereIn('status', [
                EnrollmentStatus::PASSED,
                EnrollmentStatus::WAITING_PAYMENT_FINAL,
                EnrollmentStatus::SETTLED,
                EnrollmentStatus::PERMANENT_STUDENT
            ]);

        // Filter academic_year_id
        if ($request->filled('academic_year_id')) {
            $query->whereHas('spmbTrack.spmbConfiguration', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        // Filter spmb_track_id
        if ($request->filled('spmb_track_id')) {
            $query->where('spmb_track_id', $request->spmb_track_id);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('applicant_name', fn($row) => $row->applicant->full_name ?? '-')
            ->addColumn('track_name', fn($row) => $row->spmbTrack->trackType->name ?? '-')
            ->addColumn('total_amount', function ($row) {
                $invoice = $row->invoices->first();
                return $invoice ? 'Rp ' . number_format($invoice->total_amount, 0, ',', '.') : '-';
            })
            ->addColumn('paid_amount', function ($row) {
                $invoice = $row->invoices->first();
                return $invoice ? 'Rp ' . number_format($invoice->paid_amount, 0, ',', '.') : '-';
            })
            ->addColumn('progress', function ($row) {
                $invoice = $row->invoices->first();
                if (!$invoice || $invoice->total_amount <= 0) return 0;
                return round(($invoice->paid_amount / $invoice->total_amount) * 100);
            })
            ->addColumn('status_label', function ($row) {
                return '<span class="badge ' . $row->status->badgeClass() . '">' . $row->status->label() . '</span>';
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="dropdown">
                            <button class="btn btn-sm btn-light-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item btn-detail" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-eye me-2"></i>Detail</a></li>';
                
                if ($row->status === EnrollmentStatus::PERMANENT_STUDENT) {
                    $btn .= '<li><a class="dropdown-item" href="' . route('admin.spmb.daftar-ulang.surat', $row->id) . '" target="_blank"><i class="ti ti-download me-2"></i>Unduh Surat</a></li>';
                } elseif ($row->status === EnrollmentStatus::SETTLED) {
                    // Finalisasi hanya valid dari status SETTLED (sudah lunas daftar ulang)
                    $applicantName = $row->applicant?->full_name ?? '-';
                    $btn .= '<li><a class="dropdown-item btn-finalize" href="javascript:void(0)" data-id="' . $row->id . '" data-name="' . e($applicantName) . '"><i class="ti ti-circle-check me-2"></i>Finalisasi</a></li>';
                }
                
                $btn .= '    </ul>
                        </div>';
                
                return $btn;
            })
            ->rawColumns(['status_label', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        $enrollment = ApplicantEnrollment::with([
            'applicant',
            'spmbTrack',
            'invoices' => function($q) {
                $q->where('category', 're_registration');
            },
            'invoices.payments' => function($q) {
                $q->where('status', 'confirmed');
            }
        ])->findOrFail($id);

        return response()->json($enrollment);
    }

    public function finalize(Request $request, $id)
    {
        $enrollment = ApplicantEnrollment::with(['invoices' => function($q) {
            $q->where('category', 're_registration');
        }])->findOrFail($id);

        // Validasi: Finalisasi hanya diizinkan dari status SETTLED (Lunas Daftar Ulang)
        // Sesuai State Machine: SETTLED → PERMANENT_STUDENT
        if ($enrollment->status !== EnrollmentStatus::SETTLED) {
            return response()->json([
                'success' => false,
                'message' => 'Finalisasi hanya dapat dilakukan pada pendaftar dengan status "Lunas Daftar Ulang" (Settled). Status saat ini: ' . $enrollment->status->label(),
            ], 400);
        }

        $invoice = $enrollment->invoices->first();
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice daftar ulang belum terbuat.',
            ], 400);
        }

        if ($invoice->paid_amount < $invoice->total_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran daftar ulang belum lunas. Sisa tagihan: Rp ' . number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.'),
            ], 400);
        }

        try {
            DB::transaction(function () use ($enrollment) {
                // Transisi SETTLED → PERMANENT_STUDENT sesuai State Machine
                $this->stateMachineService->transition(
                    $enrollment,
                    EnrollmentStatus::PERMANENT_STUDENT,
                    'Finalisasi daftar ulang oleh Admin — siswa resmi diterima sebagai Siswa Tetap'
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Daftar ulang berhasil difinalisasi. Status pendaftar menjadi Siswa Tetap.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadLetter($id)
    {
        $enrollment = ApplicantEnrollment::with(['applicant', 'spmbTrack.trackType'])->findOrFail($id);

        if ($enrollment->status !== EnrollmentStatus::PERMANENT_STUDENT) {
            abort(403, 'Siswa belum berstatus Siswa Tetap.');
        }

        try {
            $pdfPath = $this->pdfGeneratorService->generateEnrollmentLetter($enrollment);

            // Pastikan file benar-benar ada setelah generate
            if (!file_exists($pdfPath)) {
                throw new \RuntimeException('File PDF tidak ditemukan setelah proses generate. Path: ' . $pdfPath);
            }

            // Buat nama file yang informatif untuk download
            $applicantName    = \Illuminate\Support\Str::slug($enrollment->applicant->full_name);
            $year             = date('Y');
            $downloadFileName = 'SuratPernyataan_' . $applicantName . '_' . $year . '-' . ($year + 1) . '.pdf';

            // Gunakan download() agar browser memaksa unduh file, bukan menampilkan inline
            return response()->download($pdfPath, $downloadFileName, [
                'Content-Type' => 'application/pdf',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh surat: ' . $e->getMessage(),
            ], 500);
        }
    }
}
