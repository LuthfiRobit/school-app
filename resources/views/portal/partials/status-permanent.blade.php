<div class="card portal-status-card card-permanent border-start-success h-100 shadow-sm" x-show="status === 'permanent_student'" x-transition>
  <div class="card-body p-4 d-flex flex-column justify-content-between">
    <div>
      <div class="badge badge-success mb-3"><i class="fa-solid fa-award me-1"></i> STATUS: SISWA TETAP</div>
      <h3 class="fw-bold text-success mb-3">Selamat Bergabung di {{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }}!</h3>
      <p class="text-muted leading-relaxed mb-4">
        Selamat, seluruh proses administrasi daftar ulang Anda telah <strong>LUNAS & TERVERIFIKASI</strong>. Anda secara resmi tercatat sebagai <strong>Siswa Baru Tetap</strong> untuk Tahun Ajaran <strong>{{ $activeConfig->academicYear->year ?? '' }}</strong>.
      </p>

      <div class="p-3 bg-light rounded-xl mb-4 border border-success-subtle d-flex align-items-center">
        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
          <i class="fa-solid fa-id-card fs-4"></i>
        </div>
        <div>
          <span class="text-muted d-block small">Nomor Induk Siswa Sementara / No Registrasi Anda:</span>
          <strong class="text-primary-900 text-lg">{{ $enrollment?->enrollment_number ?? '' }}</strong>
        </div>
      </div>
    </div>

    <div>
      @if ($enrollment)
        <a href="{{ route('portal.letter.download', $enrollment->id) }}" class="btn btn-success px-4 py-2"><i class="fa-solid fa-download me-2"></i> Unduh Surat Pernyataan Siswa Tetap</a>
      @endif
    </div>
  </div>
</div>
