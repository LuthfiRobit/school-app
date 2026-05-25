<div class="card portal-status-card card-passed border-start-success h-100 shadow-sm" x-show="status === 'passed'" x-transition>
  <div class="card-body p-4 d-flex flex-column justify-content-between">
    <div>
      <div class="badge badge-success mb-3 bg-success text-white"><i class="fa-solid fa-circle-check me-1"></i> SELEKSI LULUS</div>
      <h3 class="fw-bold text-success mb-3">Selamat, Anda Dinyatakan LULUS Seleksi!</h3>
      <p class="text-muted leading-relaxed mb-4">
        Berdasarkan hasil evaluasi tim penguji SPMB {{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }}, Anda dinyatakan <strong>LULUS</strong> seleksi pada jalur pendaftaran <strong>{{ $enrollment && $enrollment->spmbTrack && $enrollment->spmbTrack->trackType ? $enrollment->spmbTrack->trackType->name : '' }}</strong>. Silakan segera selesaikan daftar ulang sebelum batas waktu.
      </p>

      <div class="alert alert-success d-flex align-items-center mb-4">
        <i class="fa-solid fa-circle-info me-3 fs-3 text-success"></i>
        <div>
          <strong>Penting:</strong> Harap lakukan konfirmasi daftar ulang dan mulai membayar tagihan uang pangkal/daftar ulang agar kursi Anda tidak dialihkan.
        </div>
      </div>
    </div>

    <div>
      @if ($enrollment)
        <a href="{{ route('portal.re-registration.show', $enrollment->id) }}" class="btn btn-success px-4 py-2"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Lakukan Daftar Ulang Sekarang</a>
      @endif
    </div>
  </div>
</div>
