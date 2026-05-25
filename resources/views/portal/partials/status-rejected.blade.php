<div class="card portal-status-card card-rejected border-start-danger h-100 shadow-sm" x-show="status === 'rejected'" x-transition>
  <div class="card-body p-4 d-flex flex-column justify-content-between">
    <div>
      <div class="badge badge-danger mb-3 bg-danger text-white"><i class="fa-solid fa-circle-xmark me-1"></i> SELEKSI SELESAI</div>
      <h3 class="fw-bold text-danger mb-3">Mohon Maaf, Anda Belum Lulus Seleksi</h3>
      <p class="text-muted leading-relaxed mb-4">
        Terima kasih atas minat dan partisipasi Anda dalam pendaftaran SPMB {{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }}. Hasil keputusan pleno menyatakan bahwa berkas/nilai seleksi Anda <strong>BELUM MEMENUHI BATAS MINIMUM</strong> untuk jalur ini.
      </p>

      <div class="mb-4 pt-3 border-top">
        <span class="text-muted d-block mb-2 small fw-semibold">Rincian Nilai Seleksi Anda:</span>
        @if($enrollment && $enrollment->assessments->isNotEmpty())
          <ul class="list-unstyled mb-0 row">
            @foreach($enrollment->assessments as $assessment)
              <li class="col-sm-6 mb-2 small">
                <i class="fa-solid fa-circle-chevron-right me-1 text-danger"></i> 
                <strong>{{ $assessment->trackAssessment->assessmentType->name ?? '' }}</strong>: 
                <span class="badge bg-danger-subtle text-danger">
                  {{ $assessment->score ? 'Nilai: ' . $assessment->score : 'Gagal' }}
                </span>
              </li>
            @endforeach
          </ul>
        @else
          <span class="small text-muted d-block"><i class="fa-solid fa-circle-info me-1"></i> Rincian nilai tidak tersedia.</span>
        @endif
      </div>

      @php
        $rejectedLog = $enrollment ? $enrollment->statusLogs()->where('to_status', 'rejected')->latest('changed_at')->first() : null;
      @endphp
      <div class="alert alert-danger d-flex align-items-center mb-4 bg-danger-subtle border-danger-subtle text-danger">
        <i class="fa-solid fa-circle-info me-3 fs-4 text-danger"></i>
        <div>
          <strong>Catatan Panitia:</strong> {{ $rejectedLog && $rejectedLog->reason ? $rejectedLog->reason : 'Skor kelulusan belum memenuhi standar minimum jalur pendaftaran yang dipilih.' }}
        </div>
      </div>

      <p class="text-muted small mb-4">
        Jangan berkecil hati! Anda masih berkesempatan mendaftar kembali di jalur lain yang saat ini masih aktif. Data diri dasar Anda tidak akan hilang dan akan disalin secara otomatis.
      </p>

      <!-- Tampilkan jalur alternatif untuk re-apply jika tersedia -->
      @if(isset($availableTracksForReapply) && $availableTracksForReapply->isNotEmpty())
        <div class="p-3 bg-light rounded-md border mb-4">
          <h6 class="fw-bold text-primary-900 small mb-2"><i class="fa-solid fa-circle-nodes text-primary-500 me-1"></i> Jalur Alternatif Aktif:</h6>
          <div class="row g-2">
            @foreach($availableTracksForReapply as $altTrack)
              <div class="col-sm-6">
                <div class="p-2 bg-white rounded border small d-flex justify-content-between align-items-center">
                  <div>
                    <strong class="text-primary-950 d-block">{{ $altTrack->trackType->name }}</strong>
                    <span class="text-muted text-xxs">Sisa Kuota: {{ max(0, $altTrack->quota - $altTrack->enrollments_count) }}</span>
                  </div>
                  <span class="badge bg-success-subtle text-success border border-success border-opacity-25 text-xxs">Tersedia</span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>

    <div>
      <a href="{{ route('portal.enrollment.reapply-select') }}" class="btn btn-outline-danger px-4 py-2"><i class="fa-solid fa-rotate-right me-2"></i> Daftar Jalur Lain (Re-Apply)</a>
    </div>
  </div>
</div>
