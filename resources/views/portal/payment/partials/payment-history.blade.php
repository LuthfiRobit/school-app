<div class="card shadow-sm border-0 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold text-primary-900 mb-3"><i class="fa-solid fa-clock-rotate-left me-2 text-primary-600"></i> Riwayat Unggah Bukti</h5>
    
    @if($payments->isEmpty())
      <div class="text-center py-4">
        <div class="text-muted mb-2"><i class="fa-solid fa-receipt fs-2"></i></div>
        <h6 class="fw-semibold text-primary-900 small mb-0">Belum ada riwayat pembayaran</h6>
        <p class="text-muted text-xxs mb-0 mt-1">Silakan lakukan pembayaran dan unggah bukti transfer Anda.</p>
      </div>
    @else
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr class="table-light">
              <th class="text-muted small ps-3">Tanggal Unggah</th>
              <th class="text-muted small text-end">Jumlah Diklaim</th>
              <th class="text-muted small text-center">Berkas Bukti</th>
              <th class="text-muted small text-center pe-3">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($payments as $payment)
              <tr>
                <td class="small text-primary-950 ps-3">
                  {{ $payment->created_at->translatedFormat('d M Y H:i') }} WIB
                </td>
                <td class="text-end fw-semibold text-primary-950 small">
                  Rp {{ number_format($payment->amount, 0, ',', '.') }}
                </td>
                <td class="text-center">
                  @if($payment->payment_proof_path)
                    <a href="{{ asset('storage/' . $payment->payment_proof_path) }}" target="_blank" class="btn btn-xs btn-outline-primary px-2 py-0.5">
                      <i class="fa-solid fa-eye me-1"></i> Lihat
                    </a>
                  @else
                    <span class="text-muted small">-</span>
                  @endif
                </td>
                <td class="text-center pe-3">
                  <span class="badge {{ $payment->status->badgeClass() }} small px-2.5 py-1">
                    {{ $payment->status->label() }}
                  </span>
                </td>
              </tr>
              @if($payment->status->value === 'rejected' && $payment->rejection_reason)
                <tr class="table-danger-subtle">
                  <td colspan="4" class="text-xxs text-danger ps-3 py-2 border-0">
                    <i class="fa-solid fa-circle-info me-1"></i> Alasan Penolakan: <strong>{{ $payment->rejection_reason }}</strong>
                  </td>
                </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
