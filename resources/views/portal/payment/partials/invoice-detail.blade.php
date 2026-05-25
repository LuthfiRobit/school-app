<div class="card shadow-sm border-0 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold text-primary-900 mb-3"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary-600"></i> Rincian Tagihan</h5>
    <div class="table-responsive">
      <table class="table table-borderless align-middle mb-0">
        <tbody>
          <tr>
            <td class="text-muted ps-0 py-2 small">Nomor Invoice</td>
            <td class="text-end fw-semibold text-primary-950 py-2 small text-monospace">{{ $invoice->invoice_number }}</td>
          </tr>
          <tr>
            <td class="text-muted ps-0 py-2 small">Deskripsi Tagihan</td>
            <td class="text-end fw-semibold text-primary-950 py-2 small">Biaya Pendaftaran Jalur {{ $enrollment->spmbTrack->trackType->name }}</td>
          </tr>
          <tr>
            <td class="text-muted ps-0 py-2 small">Batas Waktu Pembayaran</td>
            <td class="text-end fw-semibold text-primary-950 py-2 small">
              {{ $invoice->due_date ? $invoice->due_date->translatedFormat('d F Y H:i') . ' WIB' : 'Tidak Ditentukan' }}
            </td>
          </tr>
          <tr class="border-top">
            <td class="text-primary-900 fw-bold ps-0 py-3">Total Pembayaran</td>
            <td class="text-end text-error fw-bold fs-5 py-3">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card shadow-sm border-0 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold text-primary-900 mb-3"><i class="fa-solid fa-building-columns me-2 text-primary-600"></i> Rekening Tujuan Transfer</h5>
    <p class="text-muted small mb-4">Silakan lakukan transfer ke salah satu rekening yayasan berikut sebesar nominal tagihan di atas.</p>
    
    <div class="d-flex flex-column gap-3">
      @foreach($bankAccounts as $account)
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
      @endforeach
    </div>
  </div>
</div>
