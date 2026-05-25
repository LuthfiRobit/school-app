<div class="card portal-status-card card-passed border-start-success h-100 shadow-sm" x-show="status === 'passed'" x-transition>
  <div class="card-body p-4 d-flex flex-column justify-content-between">
    <div>
      <div class="badge badge-success mb-3 bg-success text-white"><i class="fa-solid fa-circle-check me-1"></i> SELEKSI LULUS</div>
      <h3 class="fw-bold text-success mb-3">Selamat, Anda Dinyatakan LULUS Seleksi!</h3>
      <p class="text-muted leading-relaxed mb-4">
        Berdasarkan hasil evaluasi tim penguji SPMB {{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }}, Anda dinyatakan <strong>LULUS</strong> seleksi pada jalur pendaftaran <strong>{{ $enrollment && $enrollment->spmbTrack && $enrollment->spmbTrack->trackType ? $enrollment->spmbTrack->trackType->name : '' }}</strong>.
      </p>

      <div class="mb-4 pt-3 border-top">
        <span class="text-muted d-block mb-2 small fw-semibold">Rincian Nilai Seleksi Anda:</span>
        @if($enrollment && $enrollment->assessments->isNotEmpty())
          <ul class="list-unstyled mb-0 row">
            @foreach($enrollment->assessments as $assessment)
              <li class="col-sm-6 mb-2 small">
                <i class="fa-solid fa-circle-chevron-right me-1 text-success"></i> 
                <strong>{{ $assessment->trackAssessment->assessmentType->name ?? '' }}</strong>: 
                <span class="badge bg-success-subtle text-success">
                  {{ $assessment->score ? 'Nilai: ' . $assessment->score : 'Lulus' }}
                </span>
              </li>
            @endforeach
          </ul>
        @else
          <span class="small text-muted d-block"><i class="fa-solid fa-circle-info me-1"></i> Rincian nilai tidak tersedia.</span>
        @endif
      </div>

      <div class="alert alert-info d-flex align-items-center mb-0">
        <i class="fa-solid fa-clock-rotate-left me-3 fs-3 text-info"></i>
        <div>
          <strong>Informasi Daftar Ulang:</strong> Jika Anda bersedia untuk melanjutkan ke tahap daftar ulang, silakan klik tombol di bawah untuk mengaktifkan tagihan daftar ulang Anda.
        </div>
      </div>
    </div>
    
    <div class="mt-4">
      @if ($enrollment)
        <form action="{{ route('portal.re-registration.start', $enrollment->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melanjutkan proses daftar ulang? Tagihan akan dibuat setelah Anda melanjutkan.');">
          @csrf
          <button type="submit" class="btn btn-success px-4 py-2">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i> Lanjutkan Daftar Ulang Sekarang
          </button>
        </form>
      @endif
    </div>
  </div>
</div>
