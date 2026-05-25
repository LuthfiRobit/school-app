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

        // Jika status passed, lakukan transisi awal ke waiting_payment_final
        if ($enrollment->status === EnrollmentStatus::PASSED) {
            try {
                $this->stateMachineService->transition($enrollment, EnrollmentStatus::WAITING_PAYMENT_FINAL, 'Calon siswa membuka halaman daftar ulang.');
                $enrollment->refresh();
            } catch (\Exception $e) {
                return redirect()->route('portal.dashboard')
                    ->with('error', 'Gagal memproses status daftar ulang: ' . $e->getMessage());
            }
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

        // Jika status passed, transisi ke waiting_payment_final dulu
        if ($enrollment->status === EnrollmentStatus::PASSED) {
            try {
                $this->stateMachineService->transition($enrollment, EnrollmentStatus::WAITING_PAYMENT_FINAL, 'Mulai mengunggah bukti cicilan daftar ulang.');
                $enrollment->refresh();
            } catch (\Exception $e) {
                return redirect()->route('portal.dashboard')
                    ->with('error', 'Gagal memproses transisi status daftar ulang.');
            }
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
     * Proses finalisasi daftar ulang oleh calon siswa untuk menjadi Siswa Tetap.
     */
    public function finalize(int $enrollmentId): RedirectResponse
    {
        $user = Auth::user();
        $applicant = $user->applicant;

        $enrollment = $this->enrollmentRepo->find($enrollmentId, ['*'], ['invoices']);

        // Verifikasi kepemilikan
        if (!$enrollment || $enrollment->applicant_id !== $applicant->id) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Pendaftaran tidak ditemukan atau Anda tidak memiliki akses.');
        }

        // Status gate: hanya boleh jika status WAITING_PAYMENT_FINAL atau SETTLED
        $allowedStatuses = [
            EnrollmentStatus::WAITING_PAYMENT_FINAL,
            EnrollmentStatus::SETTLED
        ];

        if (!in_array($enrollment->status, $allowedStatuses)) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Status pendaftaran tidak valid untuk finalisasi.');
        }

        $invoice = $enrollment->invoices->where('category', 're_registration')->first();
        if (!$invoice) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Invoice daftar ulang tidak ditemukan.');
        }

        // Validasi kelunasan
        if ($invoice->paid_amount < $invoice->total_amount) {
            return redirect()->route('portal.re-registration.show', $enrollmentId)
                ->with('error', 'Pembayaran daftar ulang belum lunas. Sisa tagihan: Rp ' . number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.'));
        }

        try {
            DB::transaction(function () use ($enrollment) {
                // Jika masih WAITING_PAYMENT_FINAL tetapi sudah lunas, transisi ke SETTLED dahulu
                if ($enrollment->status === EnrollmentStatus::WAITING_PAYMENT_FINAL) {
                    $this->stateMachineService->transition(
                        $enrollment,
                        EnrollmentStatus::SETTLED,
                        'Uang pangkal/daftar ulang terkonfirmasi lunas.'
                    );
                    $enrollment->refresh();
                }

                // Transisi ke PERMANENT_STUDENT
                if ($enrollment->status === EnrollmentStatus::SETTLED) {
                    $this->stateMachineService->transition(
                        $enrollment,
                        EnrollmentStatus::PERMANENT_STUDENT,
                        'Finalisasi daftar ulang mandiri oleh calon siswa.'
                    );
                }
            });

            return redirect()->route('portal.dashboard')
                ->with('finalize_success', true)
                ->with('success', 'Selamat! Anda telah resmi menjadi Siswa Tetap.');
        } catch (\Exception $e) {
            return redirect()->route('portal.re-registration.show', $enrollmentId)
                ->with('error', 'Gagal memproses finalisasi: ' . $e->getMessage());
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
