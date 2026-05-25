<div class="card shadow-sm border-0 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold text-primary-900 mb-3">
      <i class="fa-solid fa-chart-line me-2 text-primary-600"></i> Progres Pelunasan
    </h5>

    <div class="mb-4">
      <div class="d-flex justify-content-between mb-1.5 small">
        <span class="text-muted">Progres Pembayaran:</span>
        <strong class="text-primary-900">{{ $percentPaid }}% Lunas</strong>
      </div>
      
      <div class="progress" style="height: 12px; border-radius: 6px;">
        <div class="progress-bar transition-all duration-500 {{ $isEligible ? 'bg-success' : 'bg-warning' }}" 
             role="progressbar" 
             style="width: {{ $percentPaid }}%" 
             aria-valuenow="{{ $percentPaid }}" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
      </div>

      <div class="d-flex justify-content-between mt-2 text-xxs text-muted">
        <span>Rp {{ number_format($paidAmount, 0, ',', '.') }}</span>
        <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
      </div>
    </div>

    <!-- Alert status -->
    @if($isEligible)
      <div class="alert alert-success border-success border-opacity-25 p-3 mb-0 d-flex align-items-start gap-2.5">
        <i class="fa-solid fa-circle-check fs-5 text-success mt-0.5 animate-bounce"></i>
        <div>
          <h6 class="fw-bold text-success mb-1">Tagihan Uang Pangkal Lunas!</h6>
          <p class="text-muted small mb-0">Pembayaran Anda telah lunas 100%. Silakan menunggu verifikasi dari Admin untuk menetapkan status Anda sebagai Siswa Tetap.</p>
        </div>
      </div>
    @else
      <div class="alert alert-warning border-warning border-opacity-25 p-3 mb-0 d-flex align-items-start gap-2.5">
        <i class="fa-solid fa-circle-info fs-5 text-warning mt-0.5"></i>
        <div>
          <h6 class="fw-bold text-warning mb-1">Sisa Biaya Daftar Ulang</h6>
          <p class="text-muted small mb-0">
            Harap lunasi kekurangan sebesar <strong>Rp {{ number_format($totalAmount - $paidAmount, 0, ',', '.') }}</strong> untuk menyelesaikan proses daftar ulang.
          </p>
        </div>
      </div>
    @endif
  </div>
</div>
