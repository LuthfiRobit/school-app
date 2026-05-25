<div class="card shadow-sm border-0 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold text-primary-900 mb-3">
      <i class="fa-solid fa-building-columns me-2 text-primary-600"></i> Rekening Tujuan Transfer
    </h5>
    <p class="text-muted small mb-4">Silakan lakukan transfer ke salah satu rekening yayasan resmi berikut. Anda dapat mencicil pembayaran atau membayar lunas sekaligus.</p>
    
    <div class="d-flex flex-column gap-3">
      @forelse($bankAccounts as $account)
        <div class="p-3 bg-light rounded-md border position-relative hover-shadow-sm transition-all duration-300">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-primary text-white px-2.5 py-1 fw-bold">{{ $account->bank_name }}</span>
            <button type="button" class="btn btn-xs btn-outline-primary border-primary text-primary-600 px-2 py-1" 
                    @click="copyToClipboard('{{ $account->account_number }}', '{{ $account->bank_name }}')">
              <i class="fa-solid fa-copy me-1"></i> Salin
            </button>
          </div>
          <div class="d-flex flex-column gap-1">
            <span class="text-muted text-xxs uppercase tracking-wider">Nomor Rekening:</span>
            <strong class="text-primary-950 fs-6 text-monospace">{{ $account->account_number }}</strong>
            <span class="text-muted text-xxs uppercase tracking-wider mt-1">Atas Nama:</span>
            <strong class="text-primary-900 small">{{ $account->account_holder }}</strong>
            @if($account->branch)
              <span class="text-muted text-xxs d-block mt-0.5"><i class="fa-solid fa-location-dot me-1"></i> Cabang: {{ $account->branch }}</span>
            @endif
          </div>
        </div>
      @empty
        <div class="text-muted text-center py-3 small">Belum ada rekening bank yayasan yang dikonfigurasi. Hubungi Customer Service untuk bantuan.</div>
      @endforelse
    </div>
  </div>
</div>
