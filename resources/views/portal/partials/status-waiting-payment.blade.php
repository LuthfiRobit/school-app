<div class="card portal-status-card card-payment border-start-warning h-100 shadow-sm" x-show="status === 'waiting_payment_reg'" x-transition>
  <div class="card-body p-4 d-flex flex-column justify-content-between">
    <div>
      <div class="badge badge-warning text-white mb-3 bg-warning"><i class="fa-solid fa-wallet me-1"></i> MENUNGGU PEMBAYARAN FORMULIR</div>
      <h3 class="fw-bold text-primary-900 mb-3">Selesaikan Pembayaran Biaya Pendaftaran</h3>
      <p class="text-muted leading-relaxed mb-4">
        Pilihan jalur Anda (<strong>{{ $enrollment && $enrollment->spmbTrack && $enrollment->spmbTrack->trackType ? $enrollment->spmbTrack->trackType->name : '' }}</strong>) telah dikunci. Silakan transfer biaya registrasi pendaftaran sebelum batas waktu untuk masuk ke tahap verifikasi dokumen.
      </p>

      <div class="invoice-box p-3 bg-light rounded-md mb-4 border">
        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
          <span class="text-muted">Total Tagihan Formulir:</span>
          <strong class="text-primary-900 text-lg">
            Rp {{ $regInvoice ? number_format($regInvoice->total_amount, 0, ',', '.') : ($enrollment ? number_format($enrollment->spmbTrack->registration_fee, 0, ',', '.') : '0') }}
          </strong>
        </div>
        
        <div class="mb-0">
          <span class="text-muted d-block mb-2 small fw-semibold">Rekening Bank Yayasan/Sekolah Tujuan:</span>
          @forelse($bankAccounts as $bank)
            <div class="border rounded p-2 mb-2 bg-white">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold text-primary-600">{{ $bank->bank_name }} <small class="text-muted">({{ $bank->branch ?? 'Cabang' }})</small></span>
                <span class="text-muted small">a/n {{ $bank->account_holder }}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="text-monospace fw-bold text-dark fs-5">{{ $bank->account_number }}</span>
                <button class="btn btn-sm btn-light p-1 border" @click="navigator.clipboard.writeText('{{ $bank->account_number }}'); toastr.info('Nomor rekening disalin!')">
                  <i class="fa-regular fa-copy"></i> Salin
                </button>
              </div>
            </div>
          @empty
            <div class="text-muted small">Belum ada rekening bank yang dikonfigurasi. Hubungi Customer Service untuk bantuan.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div>
      @if ($enrollment)
        <a href="{{ route('portal.payment.show', $enrollment->id) }}" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-upload me-2"></i> Upload Bukti Pembayaran</a>
      @endif
    </div>
  </div>
</div>
