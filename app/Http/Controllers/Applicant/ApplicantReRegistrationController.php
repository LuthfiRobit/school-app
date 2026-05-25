<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\UploadPaymentProofRequest;
use App\Enums\EnrollmentStatus;
use App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface;
use App\Repositories\Interfaces\BankAccountRepositoryInterface;
use App\Services\PaymentService;
use App\Services\StateMachineService;
use App\Services\InvoiceGeneratorService;
use App\Services\PdfGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ApplicantReRegistrationController extends Controller
{
    public function __construct(
        protected ApplicantEnrollmentRepositoryInterface $enrollmentRepo,
        protected BankAccountRepositoryInterface         $bankAccountRepo,
        protected PaymentService                         $paymentService,
        protected StateMachineService                    $stateMachineService,
        protected InvoiceGeneratorService                $invoiceGeneratorService,
        protected PdfGeneratorService                   $pdfGeneratorService
    ) {}

    /**
     * Tampilkan halaman status dan progres daftar ulang beserta riwayat pembayaran.
     */
    public function show(int $enrollmentId): View|RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        // Ambil enrollment beserta relasi detail
        $enrollment = $this->enrollmentRepo->find($enrollmentId, ['*'], [
            'spmbTrack.trackType',
            'invoices.payments' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ]);

        // Verifikasi kepemilikan
        if (!$enrollment || $enrollment->applicant_id !== $applicant->id) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Status gate
        $allowedStatuses = [
            EnrollmentStatus::WAITING_PAYMENT_FINAL,
            EnrollmentStatus::SETTLED,
            EnrollmentStatus::PERMANENT_STUDENT
        ];

        if (!in_array($enrollment->status, $allowedStatuses)) {
            return redirect()->route('portal.dashboard')
                ->with('warning', 'Halaman daftar ulang tidak dapat diakses untuk status Anda saat ini: ' . $enrollment->status->label());
        }

        // Ambil invoice re_registration
        $invoice = $enrollment->invoices->where('category', 're_registration')->first();
        if (!$invoice) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Invoice daftar ulang belum dibuat.');
        }

        $totalAmount = (float) $invoice->total_amount;
        $paidAmount = (float) $invoice->paid_amount;
        $percentPaid = $totalAmount > 0 ? (int) round(($paidAmount / $totalAmount) * 100) : 0;
        $isEligible = $this->invoiceGeneratorService->isEligibleForFinalization($enrollment);

        // Rekening bank aktif
        $bankAccounts = $this->bankAccountRepo->all(['*'])->where('is_active', 1)->values();

        // Riwayat upload pembayaran
        $payments = $invoice->payments;

        return view('portal.re-registration.show', compact(
            'enrollment',
            'invoice',
            'bankAccounts',
            'payments',
            'percentPaid',
            'isEligible',
            'totalAmount',
            'paidAmount'
        ));
    }

    /**
     * Memulai proses daftar ulang (dari PASSED ke WAITING_PAYMENT_FINAL).
     */
    public function start(int $enrollmentId): RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        $enrollment = $this->enrollmentRepo->find($enrollmentId);

        if (!$enrollment || $enrollment->applicant_id !== $applicant->id) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($enrollment->status === EnrollmentStatus::PASSED) {
            try {
                $this->stateMachineService->transition(
                    $enrollment, 
                    EnrollmentStatus::WAITING_PAYMENT_FINAL, 
                    'Siswa bersedia melanjutkan proses daftar ulang'
                );
                return redirect()->route('portal.re-registration.show', $enrollment->id)
                    ->with('success', 'Proses daftar ulang telah dimulai. Silakan selesaikan pembayaran tagihan.');
            } catch (\Exception $e) {
                return redirect()->route('portal.dashboard')
                    ->with('error', 'Gagal memulai daftar ulang: ' . $e->getMessage());
            }
        }

        return redirect()->route('portal.dashboard');
    }

    /**
     * Proses pengunggahan bukti cicilan / pembayaran daftar ulang oleh calon siswa.
     */
    public function upload(UploadPaymentProofRequest $request, int $enrollmentId): RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        $enrollment = $this->enrollmentRepo->find($enrollmentId, ['*'], ['invoices']);

        // Verifikasi kepemilikan
        if (!$enrollment || $enrollment->applicant_id !== $applicant->id) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Status gate: Hanya boleh upload ketika WAITING_PAYMENT_FINAL
        if ($enrollment->status !== EnrollmentStatus::WAITING_PAYMENT_FINAL) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Status pendaftaran tidak valid untuk mengunggah bukti daftar ulang.');
        }

        $invoice = $enrollment->invoices->where('category', 're_registration')->first();
        if (!$invoice) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Invoice daftar ulang tidak ditemukan.');
        }

        try {
            DB::transaction(function () use ($request, $invoice) {
                $this->paymentService->submitProof(
                    $invoice,
                    $request->file('payment_proof'),
                    (float) $request->amount
                );
            });

            return redirect()->route('portal.re-registration.show', $enrollmentId)
                ->with('success', 'Bukti pembayaran daftar ulang berhasil diunggah. Menunggu konfirmasi panitia.');
        } catch (\Exception $e) {
            return redirect()->route('portal.re-registration.show', $enrollmentId)
                ->with('error', 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Unduh surat pernyataan siswa tetap PDF.
     */
    public function downloadLetter(int $enrollmentId): Response|RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        $enrollment = $this->enrollmentRepo->find($enrollmentId, ['*'], ['applicant', 'spmbTrack.trackType']);

        // Verifikasi kepemilikan
        if (!$enrollment || $enrollment->applicant_id !== $applicant->id) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Status gate: harus sudah PERMANENT_STUDENT
        if ($enrollment->status !== EnrollmentStatus::PERMANENT_STUDENT) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Surat pernyataan hanya dapat diunduh jika Anda sudah terdaftar sebagai Siswa Tetap.');
        }

        try {
            $pdfPath = $this->pdfGeneratorService->generateEnrollmentLetter($enrollment);

            if (!file_exists($pdfPath)) {
                throw new \RuntimeException('File surat pernyataan tidak ditemukan setelah digenerate.');
            }

            $applicantSlug = \Illuminate\Support\Str::slug($enrollment->applicant->full_name);
            $year = date('Y');
            $downloadFileName = 'SuratPernyataan_' . $applicantSlug . '_' . $year . '-' . ($year + 1) . '.pdf';

            return response()->download($pdfPath, $downloadFileName, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Gagal mengunduh surat pernyataan: ' . $e->getMessage());
        }
    }
}
