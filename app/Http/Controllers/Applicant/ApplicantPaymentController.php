<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Applicant\UploadPaymentProofRequest;
use App\Enums\EnrollmentStatus;
use App\Repositories\Interfaces\ApplicantEnrollmentRepositoryInterface;
use App\Repositories\Interfaces\BankAccountRepositoryInterface;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApplicantPaymentController extends Controller
{
    public function __construct(
        protected ApplicantEnrollmentRepositoryInterface $enrollmentRepo,
        protected BankAccountRepositoryInterface         $bankAccountRepo,
        protected PaymentService                         $paymentService
    ) {}

    /**
     * Tampilkan halaman tagihan dan status pembayaran pendaftaran.
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
        if ($enrollment->status !== EnrollmentStatus::WAITING_PAYMENT_REG) {
            return redirect()->route('portal.dashboard')
                ->with('info', 'Status pendaftaran Anda saat ini adalah: ' . $enrollment->status->label());
        }

        // Ambil invoice category registration
        $invoice = $enrollment->invoices->where('category', 'registration')->first();
        if (!$invoice) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Invoice pendaftaran tidak ditemukan.');
        }

        // Rekening bank aktif
        $bankAccounts = $this->bankAccountRepo->all(['*'])->where('is_active', 1)->values();

        // Riwayat upload pembayaran
        $payments = $invoice->payments;

        return view('portal.payment.show', compact('enrollment', 'invoice', 'bankAccounts', 'payments'));
    }

    /**
     * Proses pengunggahan bukti pembayaran oleh calon siswa.
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

        // Status gate
        if ($enrollment->status !== EnrollmentStatus::WAITING_PAYMENT_REG) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Status pendaftaran tidak valid untuk mengunggah bukti pembayaran.');
        }

        $invoice = $enrollment->invoices->where('category', 'registration')->first();
        if (!$invoice) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Invoice pendaftaran tidak ditemukan.');
        }

        try {
            DB::transaction(function () use ($request, $invoice) {
                $this->paymentService->submitProof(
                    $invoice,
                    $request->file('payment_proof'),
                    (float) $request->amount
                );
            });

            return redirect()->route('portal.payment.show', $enrollmentId)
                ->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi panitia.');
        } catch (\Exception $e) {
            return redirect()->route('portal.payment.show', $enrollmentId)
                ->with('error', 'Gagal mengunggah bukti pembayaran: ' . $e->getMessage());
        }
    }
}
