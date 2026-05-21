<?php

namespace App\Http\Controllers\Admin\Spmb;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Spmb\ConfirmPaymentRequest;
use App\Http\Requests\Admin\Spmb\ManualPaymentRequest;
use App\Http\Requests\Admin\Spmb\RejectPaymentRequest;
use App\Models\Invoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Services\PaymentService;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository,
        protected InvoiceRepositoryInterface $invoiceRepository,
        protected PaymentService $paymentService
    ) {}

    /**
     * Tampilan utama antrean dan riwayat verifikasi pembayaran.
     */
    public function index()
    {
        // Fetch unpaid/partial invoices for manual payment dropdown
        $unpaidInvoices = Invoice::whereIn('status', ['unpaid', 'partial'])
            ->with(['enrollment.applicant', 'enrollment.spmbTrack.trackType'])
            ->get();

        return view('admin.spmb.pembayaran.index', compact('unpaidInvoices'));
    }

    /**
     * Get data antrean verifikasi untuk DataTables Server-side.
     */
    public function getData(Request $request)
    {
        $query = $this->paymentRepository->query(['invoice.enrollment.applicant', 'invoice.enrollment.spmbTrack.trackType', 'confirmedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('input_method')) {
            $query->where('input_method', $request->input_method);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actions = '<div class="dropdown">
                                <button class="btn btn-sm btn-light-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="ti ti-settings"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item btn-detail" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-eye me-2"></i>Detail</a></li>';
                
                if ($row->status === PaymentStatus::PENDING) {
                    $actions .= '<li><a class="dropdown-item btn-confirm text-success" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-check me-2"></i>Konfirmasi</a></li>
                                 <li><a class="dropdown-item btn-reject text-danger" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-x me-2"></i>Tolak</a></li>';
                }

                $actions .= '</ul></div>';
                return $actions;
            })
            ->addColumn('enrollment_number', function ($row) {
                return $row->invoice && $row->invoice->enrollment 
                    ? ($row->invoice->enrollment->enrollment_number ?: '<span class="text-muted italic">Draft</span>') 
                    : '-';
            })
            ->addColumn('applicant_name', function ($row) {
                return $row->invoice && $row->invoice->enrollment && $row->invoice->enrollment->applicant 
                    ? $row->invoice->enrollment->applicant->full_name 
                    : '-';
            })
            ->addColumn('invoice_number', function ($row) {
                return $row->invoice ? $row->invoice->invoice_number : '-';
            })
            ->addColumn('amount_formatted', function ($row) {
                return 'Rp ' . number_format($row->amount, 0, ',', '.');
            })
            ->addColumn('confirmed_amount_formatted', function ($row) {
                return $row->confirmed_amount !== null 
                    ? 'Rp ' . number_format($row->confirmed_amount, 0, ',', '.') 
                    : '<span class="text-muted italic">-</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;
                return '<span class="badge ' . $status->badgeClass() . '">' . $status->label() . '</span>';
            })
            ->addColumn('input_method_label', function ($row) {
                return $row->input_method === 'upload' 
                    ? '<span class="badge bg-light-info text-info"><i class="ti ti-upload me-1"></i>Upload Bukti</span>' 
                    : '<span class="badge bg-light-success text-success"><i class="ti ti-cash me-1"></i>Tunai/Manual</span>';
            })
            ->addColumn('created_at_formatted', function ($row) {
                return $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-';
            })
            ->rawColumns(['action', 'enrollment_number', 'status_badge', 'input_method_label', 'confirmed_amount_formatted'])
            ->make(true);
    }

    /**
     * Detail pembayaran (JSON) beserta preview bukti bayar.
     */
    public function show($id)
    {
        $payment = $this->paymentRepository->find($id, ['*'], ['invoice.enrollment.applicant', 'invoice.enrollment.spmbTrack.trackType', 'confirmedBy']);

        if (!$payment) {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        // Add visual properties
        $payment->proof_url = $payment->payment_proof_path ? asset('storage/' . $payment->payment_proof_path) : null;
        $payment->amount_formatted = 'Rp ' . number_format($payment->amount, 0, ',', '.');
        $payment->invoice_total_formatted = 'Rp ' . number_format($payment->invoice->total_amount, 0, ',', '.');
        $payment->invoice_paid_formatted = 'Rp ' . number_format($payment->invoice->paid_amount, 0, ',', '.');
        $payment->invoice_unpaid_formatted = 'Rp ' . number_format(max(0, $payment->invoice->total_amount - $payment->invoice->paid_amount), 0, ',', '.');

        return response()->json($payment);
    }

    /**
     * Konfirmasi pembayaran.
     */
    public function confirm(ConfirmPaymentRequest $request, $id)
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        try {
            $this->paymentService->confirmProof($payment, $request->confirmed_amount);
            return response()->json(['message' => 'Pembayaran berhasil dikonfirmasi dan tagihan diperbarui']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Tolak pembayaran.
     */
    public function reject(RejectPaymentRequest $request, $id)
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        try {
            $this->paymentService->rejectProof($payment, $request->rejection_reason);
            return response()->json(['message' => 'Bukti pembayaran berhasil ditolak']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Input pembayaran manual (cash).
     */
    public function storeManual(ManualPaymentRequest $request)
    {
        $invoice = Invoice::find($request->invoice_id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice tidak ditemukan'], 404);
        }

        try {
            $this->paymentService->recordManual($invoice, $request->amount, $request->notes ?: '');
            return response()->json(['message' => 'Pembayaran manual berhasil dicatat']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
