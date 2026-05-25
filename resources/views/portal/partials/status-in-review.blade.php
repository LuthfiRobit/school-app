<div class="card portal-status-card card-selection border-start-accent h-100 shadow-sm" x-show="status === 'verified_reg' || status === 'in_review'" x-transition>
  <div class="card-body p-4 d-flex flex-column justify-content-between">
    <div>
      @if(!$announcementVisible)
        <div class="badge badge-warning text-dark mb-3 bg-warning"><i class="fa-solid fa-hourglass-half me-1"></i> MENUNGGU PENGUMUMAN</div>
        <h3 class="fw-bold text-primary-900 mb-3">Hasil Seleksi Sedang Dievaluasi</h3>
        <p class="text-muted leading-relaxed mb-4">
          Seluruh berkas pendaftaran dan ujian Anda telah diterima secara lengkap. Saat ini panitia sedang melakukan proses penilaian dan keputusan akhir rapat pleno kelulusan.
        </p>
        
        <div class="p-4 bg-light rounded-xl border mb-4 text-center">
          <i class="fa-solid fa-bullhorn fs-2 text-primary mb-3"></i>
          <h6 class="fw-bold mb-1 text-primary-900">Hasil seleksi belum diumumkan</h6>
          <p class="text-muted small mb-0">Pantau terus halaman ini untuk melihat hasil keputusan kelulusan Anda secara resmi.</p>
          
          @if($enrollment?->announcement_visible_at)
            <div class="mt-3 pt-3 border-top">
              <span class="text-muted small d-block mb-1">Jadwal Pengumuman Resmi:</span>
              <strong class="text-primary-950 fs-5">
                {{ $enrollment->announcement_visible_at->translatedFormat('d F Y') }} pukul {{ $enrollment->announcement_visible_at->format('H:i') }} WIB
              </strong>
            </div>
          @endif
        </div>
      @else
        <!-- Actual In Review / Verified Reg info -->
        <div class="badge badge-primary bg-accent mb-3"><i class="fa-solid fa-calendar-check me-1"></i> PROSES SELEKSI JALUR</div>
        <h3 class="fw-bold text-primary-900 mb-3">Ikuti Tahap Seleksi Jalur Pendaftaran</h3>
        <p class="text-muted leading-relaxed mb-4">
          Pembayaran registrasi dan berkas pendaftaran Anda dinyatakan <strong>SAH & LOLOS VERIFIKASI</strong>. Anda kini memasuki tahap seleksi evaluasi berkas, pengujian, atau tes potensi sesuai rincian jalur:
        </p>

        <div class="schedule-box p-3 bg-light rounded-md mb-4 border">
          <div class="row g-3">
            <div class="col-sm-6">
              <span class="text-muted d-block small">Jalur SPMB:</span>
              <strong class="text-primary-900">{{ $enrollment && $enrollment->spmbTrack && $enrollment->spmbTrack->trackType ? $enrollment->spmbTrack->trackType->name : '' }}</strong>
            </div>
            <div class="col-sm-6">
              <span class="text-muted d-block small">Nomor Pendaftaran:</span>
              <strong class="text-primary-900 text-monospace">{{ $enrollment?->enrollment_number ?? '' }}</strong>
            </div>
            <div class="col-12 mt-2 pt-2 border-top">
              <span class="text-muted d-block mb-1 small fw-semibold">Komponen Penilaian Seleksi:</span>
              @if($enrollment && $enrollment->assessments->isNotEmpty())
                <ul class="list-unstyled mb-0 row">
                  @foreach($enrollment->assessments as $assessment)
                    <li class="col-sm-6 mb-1 small">
                      <i class="fa-solid fa-circle-chevron-right me-1 text-primary"></i> 
                      <strong>{{ $assessment->trackAssessment->assessmentType->name ?? '' }}</strong>: 
                      <span class="badge {{ $assessment->score ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                        {{ $assessment->score ? 'Nilai: ' . $assessment->score : 'Belum Dinilai' }}
                      </span>
                    </li>
                  @endforeach
                </ul>
              @else
                <span class="small text-muted d-block"><i class="fa-solid fa-circle-info me-1"></i> Penilaian berkas sedang dihitung dan dievaluasi oleh tim penguji.</span>
              @endif
            </div>
          </div>
        </div>
      @endif
    </div>

    @if($announcementVisible)
      <div>
        <button class="btn btn-primary px-4 py-2" @click="toastr.info('Mengunduh kartu peserta...');"><i class="fa-solid fa-file-pdf me-2"></i> Unduh Kartu Ujian Peserta</button>
      </div>
    @endif
  </div>
</div>
