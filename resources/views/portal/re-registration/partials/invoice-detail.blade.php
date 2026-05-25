<div class="card shadow-sm border-0 mb-4">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
      <h5 class="fw-bold text-primary-900 mb-0">
        <i class="fa-solid fa-file-invoice-dollar me-2 text-primary-600"></i> Rincian Tagihan Daftar Ulang
      </h5>
      <span class="badge {{ $invoice->status->badgeClass() }} small px-2.5 py-1">
        {{ $invoice->status->label() }}
      </span>
    </div>

    <!-- Info tagihan utama -->
    <div class="row g-3 mb-4 small">
      <div class="col-sm-6">
        <span class="text-muted d-block mb-0.5">Nomor Invoice:</span>
        <strong class="text-primary-950 text-monospace">{{ $invoice->invoice_number }}</strong>
      </div>
      <div class="col-sm-6 text-sm-end">
        <span class="text-muted d-block mb-0.5">Batas Waktu Pelunasan:</span>
        <strong class="text-primary-950">
          {{ $invoice->due_date ? $invoice->due_date->translatedFormat('d F Y H:i') . ' WIB' : 'Tidak Ditentukan' }}
        </strong>
      </div>
    </div>

    <!-- Tabel Rincian Komponen Biaya -->
    <div class="table-responsive mb-4">
      <table class="table table-sm table-hover align-middle mb-0">
        <thead>
          <tr class="table-light small">
            <th class="text-muted py-2 ps-3" style="width: 70%;">Komponen Biaya</th>
            <th class="text-muted py-2 text-end pe-3" style="width: 30%;">Nominal</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoice->items as $item)
            <tr class="small text-primary-950">
              <td class="py-2.5 ps-3 fw-semibold">{{ $item->description }}</td>
              <td class="py-2.5 text-end pe-3">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr class="small text-muted">
              <td colspan="2" class="text-center py-3">Tidak ada rincian komponen biaya.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Ringkasan Biaya Akhir -->
    <div class="p-3 bg-light rounded-md border small">
      <div class="d-flex justify-content-between mb-2">
        <span class="text-muted">Total Tagihan:</span>
        <span class="fw-bold text-primary-950">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
      </div>
      <div class="d-flex justify-content-between mb-2">
        <span class="text-muted">Telah Dibayar (Terkonfirmasi):</span>
        <span class="fw-bold text-success">Rp {{ number_format($paidAmount, 0, ',', '.') }}</span>
      </div>
      <div class="d-flex justify-content-between border-top pt-2">
        <strong class="text-primary-900">Sisa Tagihan Pelunasan:</strong>
        <strong class="text-error fs-6">Rp {{ number_format(max(0, $totalAmount - $paidAmount), 0, ',', '.') }}</strong>
      </div>
    </div>
  </div>
</div>
