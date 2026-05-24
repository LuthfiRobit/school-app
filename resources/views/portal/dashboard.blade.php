@extends('portal.layouts.app')

@section('title', 'Dashboard')

@php
    $initials = '';
    if ($applicant) {
        $names = explode(' ', $applicant->full_name);
        $initials = strtoupper(substr($names[0] ?? '', 0, 1) . substr($names[1] ?? '', 0, 1));
    }
    
    // registration invoice
    $regInvoice = $enrollment ? $enrollment->invoices->where('category', 'registration')->first() : null;
    
    // re_registration invoice
    $reRegInvoice = $enrollment ? $enrollment->invoices->where('category', 're_registration')->first() : null;
    $totalAmount = $reRegInvoice ? (float)$reRegInvoice->total_amount : 0.0;
    $paidAmount = $reRegInvoice ? (float)$reRegInvoice->paid_amount : 0.0;
    $percentPaid = $totalAmount > 0 ? (int)round(($paidAmount / $totalAmount) * 100) : 0;
@endphp

@section('content')
<div x-data="dashboardApp()" class="w-100">

  <!-- Confetti Container for Permanent Student status -->
  <div class="confetti-container" x-show="status === 'permanent_student' && showConfetti" style="display: none;">
    <template x-for="i in 50" :key="i">
      <div class="confetti-piece"
        :style="'left: ' + (Math.random() * 100) + '%; transform: rotate(' + (Math.random() * 360) + 'deg); background-color: ' + getRandomColor() + '; animation-delay: ' + (Math.random() * 3) + 's; animation-duration: ' + (2 + Math.random() * 2) + 's;'">
      </div>
    </template>
  </div>

  <!-- FLOATING PROTOTYPE STATUS SIMULATOR (LOCAL ENVIRONMENT ONLY) -->
  @if (config('app.env') === 'local')
    <div class="portal-simulator-widget shadow-lg" x-data="{ collapsed: true }" :class="{ 'collapsed': collapsed }" style="z-index: 1050;">
      <div class="widget-header d-flex align-items-center justify-content-between border-bottom pb-2 mb-2 bg-primary text-white rounded-top p-2" style="margin: -10px -10px 10px -10px;">
        <span class="fw-bold small"><i class="fa-solid fa-vial me-1"></i> SIMULATOR STATUS</span>
        <button class="btn btn-sm text-white p-0" @click="collapsed = !collapsed">
          <i class="fa-solid" :class="collapsed ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        </button>
      </div>

      <div class="widget-content" x-show="!collapsed" style="display: none;">
        <p class="small text-muted mb-2">Simulasikan keadaan dashboard untuk menguji seluruh visual flow pendaftaran:</p>

        <div class="d-grid gap-1">
          <button class="btn btn-sm btn-outline-primary text-start" :class="{ 'active bg-primary text-white': status === 'no_enrollment' }" @click="setStatus('no_enrollment')">0. Belum Mendaftar</button>
          <button class="btn btn-sm btn-outline-primary text-start" :class="{ 'active bg-primary text-white': status === 'draft' }" @click="setStatus('draft')">1. Draf Formulir</button>
          <button class="btn btn-sm btn-outline-warning text-start" :class="{ 'active bg-warning text-dark': status === 'waiting_payment_reg' }" @click="setStatus('waiting_payment_reg')">2. Menunggu Bayar Reg</button>
          <button class="btn btn-sm btn-outline-info text-start" :class="{ 'active bg-info text-white': status === 'registered' }" @click="setStatus('registered')">3. Berkas Terkirim (Verifikasi)</button>
          <button class="btn btn-sm btn-outline-secondary text-start" :class="{ 'active bg-secondary text-white': status === 'verified_reg' }" @click="setStatus('verified_reg')">4. Seleksi CBT / Tes</button>
          <button class="btn btn-sm btn-outline-success text-start" :class="{ 'active bg-success text-white': status === 'passed' }" @click="setStatus('passed')">5. Seleksi Lulus</button>
          <button class="btn btn-sm btn-outline-warning text-start" :class="{ 'active bg-warning text-dark': status === 'waiting_payment_final' }" @click="setStatus('waiting_payment_final')">6. Cicil Daftar Ulang</button>
          <button class="btn btn-sm btn-outline-danger text-start" :class="{ 'active bg-danger text-white': status === 'rejected' }" @click="setStatus('rejected')">7. Seleksi Gagal</button>
          <button class="btn btn-sm btn-outline-success text-start" :class="{ 'active bg-success text-white fw-bold': status === 'permanent_student' }" @click="setStatus('permanent_student')">8. Siswa Baru Tetap 🎉</button>
          
          <button class="btn btn-xs btn-dark text-white mt-2 w-100" x-show="isSimulated" @click="resetToDb()" style="display: none;">
            <i class="fa-solid fa-arrows-rotate me-1"></i> Reset ke Status Asli DB
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- ALERT NOTIFIKASI SIMULATOR AKTIF -->
  <div class="alert alert-warning border-start-warning d-flex align-items-center justify-content-between mb-4 shadow-sm" x-show="isSimulated" style="display: none;" data-aos="fade-down">
    <div class="d-flex align-items-center">
      <i class="fa-solid fa-triangle-exclamation me-3 fs-4 text-warning"></i>
      <div>
        <strong>Mode Simulasi Aktif:</strong> Visual di bawah adalah simulasi status 
        <span class="badge bg-warning text-dark text-capitalize ms-1" x-text="status.replace(/_/g, ' ')"></span>.
      </div>
    </div>
    <button class="btn btn-sm btn-dark text-white px-3 py-1" @click="resetToDb()">
      <i class="fa-solid fa-arrows-rotate me-1"></i> Kembalikan ke DB
    </button>
  </div>

  <!-- STEPPER TIMELINE STATUS -->
  <div class="card mb-4 p-4 shadow-sm" data-aos="fade-down" data-aos-duration="600" x-show="status !== 'no_enrollment'">
    <h6 class="fw-bold mb-4 text-primary text-uppercase tracking-wider">
      <i class="fa-solid fa-route me-2"></i> Alur Pendaftaran Anda
    </h6>

    <!-- Container Scroll untuk Mobile agar tetap rapi -->
    <div class="portal-stepper-scroll-container px-2 pb-2">
      <div class="position-relative mx-auto mt-2 mb-3" style="max-width: 640px; min-width: 520px;">

        <!-- 1. GARIS PENGHUBUNG (BACKGROUND & ACTIVE) -->
        <div class="position-absolute translate-middle-y" style="height: 3px; left: 32px; right: 32px; top: 16px; z-index: 0;">
          <div class="position-absolute top-0 start-0 w-100 bg-secondary-subtle" style="height: 3px;"></div>
          <div class="position-absolute top-0 start-0 bg-primary transition-all"
            style="height: 3px; transition: width 0.4s ease;"
            :style="'width: ' + ((activeStep - 1) * 20) + '%'">
          </div>
        </div>

        <!-- 2. WIDGET STEP INDICATOR -->
        <div class="d-flex justify-content-between position-relative" style="z-index: 1;">

          <!-- Step 1: Daftar -->
          <div class="text-center" style="width: 64px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
              :class="activeStep > 1 ? 'bg-success text-white border-success' : (activeStep === 1 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle')"
              style="width: 32px; height: 32px;">
              <i class="fa-solid fa-check" x-show="activeStep > 1"></i>
              <span x-show="activeStep === 1">1</span>
            </div>
            <span class="small fw-semibold d-block text-truncate" :class="activeStep === 1 ? 'text-primary' : 'text-secondary'">Daftar</span>
          </div>

          <!-- Step 2: Bayar -->
          <div class="text-center" style="width: 64px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
              :class="activeStep > 2 ? 'bg-success text-white border-success' : (activeStep === 2 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle')"
              style="width: 32px; height: 32px;">
              <i class="fa-solid fa-check" x-show="activeStep > 2"></i>
              <span x-show="activeStep <= 2">2</span>
            </div>
            <span class="small fw-semibold d-block text-truncate" :class="activeStep === 2 ? 'text-primary' : 'text-secondary'">Bayar</span>
          </div>

          <!-- Step 3: Seleksi -->
          <div class="text-center" style="width: 64px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
              :class="activeStep > 3 ? 'bg-success text-white border-success' : (activeStep === 3 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle')"
              style="width: 32px; height: 32px;">
              <i class="fa-solid fa-check" x-show="activeStep > 3"></i>
              <span x-show="activeStep <= 3">3</span>
            </div>
            <span class="small fw-semibold d-block text-truncate" :class="activeStep === 3 ? 'text-primary' : 'text-secondary'">Seleksi</span>
          </div>

          <!-- Step 4: Kelulusan / Gagal -->
          <div class="text-center" style="width: 64px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
              :class="status === 'rejected' ? 'bg-danger text-white border-danger shadow' : (activeStep > 4 || status === 'permanent_student' ? 'bg-success text-white border-success' : (activeStep === 4 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle'))"
              style="width: 32px; height: 32px;">
              <i class="fa-solid fa-xmark" x-show="status === 'rejected'"></i>
              <i class="fa-solid fa-check" x-show="activeStep > 4 && status !== 'rejected'"></i>
              <span x-show="activeStep <= 4 && status !== 'rejected'">4</span>
            </div>
            <span class="small fw-semibold d-block text-truncate" :class="status === 'rejected' ? 'text-danger' : (activeStep === 4 ? 'text-primary' : 'text-secondary')" x-text="status === 'rejected' ? 'Gagal' : 'Kelulusan'">Kelulusan</span>
          </div>

          <!-- Step 5: Daftar Ulang -->
          <div class="text-center" style="width: 64px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
              :class="status === 'permanent_student' || activeStep > 5 ? 'bg-success text-white border-success' : (activeStep === 5 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle')"
              style="width: 32px; height: 32px;">
              <i class="fa-solid fa-check" x-show="activeStep > 5 || status === 'permanent_student'"></i>
              <span x-show="activeStep <= 5 && status !== 'permanent_student'">5</span>
            </div>
            <span class="small fw-semibold d-block text-truncate" :class="activeStep === 5 ? 'text-primary' : 'text-secondary'">Daftar Ulang</span>
          </div>

          <!-- Step 6: Siswa Baru -->
          <div class="text-center" style="width: 64px;">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
              :class="status === 'permanent_student' ? 'bg-success text-white border-success shadow' : 'bg-light text-muted border-secondary-subtle'"
              style="width: 32px; height: 32px;">
              <i class="fa-solid fa-graduation-cap" x-show="status === 'permanent_student'"></i>
              <span x-show="status !== 'permanent_student'">6</span>
            </div>
            <span class="small fw-semibold d-block text-truncate" :class="status === 'permanent_student' ? 'text-success' : 'text-secondary'">Siswa Baru</span>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- MAIN GRID: WELCOME CARD & SIDEBAR INFO -->
  <div class="row g-4">

    <!-- LEFT COLUMN: WELCOME CARD / DYNAMIC CONTENT -->
    <div class="col-lg-8" data-aos="fade-right" data-aos-duration="600">

      <!-- 0. WELCOME / NO ENROLLMENT CARD -->
      <div class="card portal-status-card border-start-primary h-100 shadow-sm" x-show="status === 'no_enrollment'" x-transition>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="badge bg-primary text-white mb-3"><i class="fa-solid fa-graduation-cap me-1"></i> PENDAFTARAN SPMB</div>
            <h3 class="fw-bold text-primary-900 mb-3">Selamat Datang di Portal SPMB!</h3>
            <p class="text-muted leading-relaxed mb-4">
              Halo, <strong>{{ $applicant->full_name }}</strong>! Anda belum memulai pendaftaran untuk tahun pelajaran <strong>{{ $activeConfig->academicYear->year ?? '' }}</strong>.
              Silakan pilih jalur pendaftaran yang tersedia untuk memulai langkah Anda.
            </p>

            <div class="alert alert-info d-flex align-items-center mb-4 bg-primary-light border-primary-200">
              <i class="fa-solid fa-circle-info me-3 fs-4 text-primary"></i>
              <div class="text-primary-900">
                Panitia SPMB {{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }} siap memandu Anda. Proses pendaftaran mudah, transparan, dan dapat dipantau langsung dari dashboard ini.
              </div>
            </div>
          </div>

          <div>
            @if ($activeConfig)
              <a href="{{ route('portal.enrollment.select-track') }}" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-file-signature me-2"></i> Pilih Jalur & Mulai Daftar</a>
            @else
              <button class="btn btn-secondary px-4 py-2" disabled><i class="fa-solid fa-ban me-2"></i> Pendaftaran Belum Dibuka</button>
            @endif
          </div>
        </div>
      </div>

      <!-- 1. DRAFT CARD -->
      <div class="card portal-status-card card-draft border-start-primary h-100 shadow-sm" x-show="status === 'draft'" x-transition>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="badge badge-secondary mb-3"><i class="fa-solid fa-pen-clip me-1"></i> DRAF FORMULIR</div>
            <h3 class="fw-bold text-primary-900 mb-3">Lengkapi Formulir Pendaftaran Anda</h3>
            <p class="text-muted leading-relaxed mb-4">
              Halo, <strong>{{ $applicant->full_name }}</strong>! Akun Anda telah aktif. Langkah berikutnya adalah melengkapi data diri, berkas pendukung, dan data akademik formulir Anda pada jalur pendaftaran <strong>{{ $enrollment && $enrollment->spmbTrack && $enrollment->spmbTrack->trackType ? $enrollment->spmbTrack->trackType->name : '' }}</strong>.
            </p>

            <div class="alert alert-primary d-flex align-items-center mb-4">
              <i class="fa-solid fa-triangle-exclamation me-3 fs-4 text-primary-600"></i>
              <div>
                Data formulir Anda masih tersimpan sebagai draf. Selesaikan pengisian dan klik kirim agar panitia dapat memproses berkas Anda.
              </div>
            </div>
          </div>

          <div>
            @if ($enrollment)
              <a href="{{ route('portal.enrollment.form', $enrollment->spmb_track_id) }}" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-file-signature me-2"></i> Lanjutkan Pengisian Formulir</a>
            @endif
          </div>
        </div>
      </div>

      <!-- 2. WAITING PAYMENT REGISTRATION CARD -->
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

      <!-- 3. REGISTERED (Berkas Dikirim, Menunggu Verifikasi) CARD -->
      <div class="card portal-status-card card-review border-start-info h-100 shadow-sm" x-show="status === 'registered'" x-transition>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="badge badge-primary mb-3 bg-info text-white"><i class="fa-solid fa-arrows-spin me-1"></i> BUKTI PEMBAYARAN DIUNGHAH</div>
            <h3 class="fw-bold text-primary-900 mb-3">Berkas Pembayaran Sedang Diperiksa Admin</h3>
            <p class="text-muted leading-relaxed mb-4">
              Terima kasih telah mengunggah bukti pembayaran dan melengkapi berkas pendaftaran Anda. Saat ini panitia SPMB {{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }} sedang memvalidasi transaksi dan kelengkapan dokumen Anda.
            </p>

            <div class="p-4 bg-primary-light rounded-xl text-center border border-primary-200 mb-4">
              <div class="spinner-border text-primary-600 mb-3" role="status" style="width: 2rem; height: 2rem;">
                <span class="visually-hidden">Loading...</span>
              </div>
              <h6 class="fw-bold mb-1 text-primary-900">Proses Validasi Administrasi</h6>
              <p class="text-muted small mb-0">Estimasi verifikasi pembayaran selesai dalam 1-2 hari kerja.</p>
            </div>
          </div>

          <div>
            <a href="https://wa.me/{{ $schoolIdentity->whatsapp ?? '6281234567890' }}" target="_blank" class="btn btn-outline-secondary px-4 py-2 border-primary text-primary-600">
              <i class="fa-brands fa-whatsapp me-2"></i> Hubungi Customer Service
            </a>
          </div>
        </div>
      </div>

      <!-- 4. SELECTION / VERIFIED REG (Jadwal CBT/Seleksi Tersedia) CARD -->
      <div class="card portal-status-card card-selection border-start-accent h-100 shadow-sm" x-show="status === 'verified_reg' || status === 'in_review' || status === 'waiting_list'" x-transition>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
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
                  <strong class="text-primary-900 text-monospace">{{ $enrollment->enrollment_number ?? '' }}</strong>
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
          </div>

          <div>
            <button class="btn btn-primary px-4 py-2" @click="toastr.info('Mengunduh kartu peserta...');"><i class="fa-solid fa-file-pdf me-2"></i> Unduh Kartu Ujian Peserta</button>
          </div>
        </div>
      </div>

      <!-- 5. PASSED (Lulus Seleksi) CARD -->
      <div class="card portal-status-card card-passed border-start-success h-100 shadow-sm" x-show="status === 'passed'" x-transition>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="badge badge-success mb-3"><i class="fa-solid fa-circle-check me-1"></i> SELEKSI LULUS</div>
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

      <!-- 6. PROCESS RE-REGISTRATION (Cicilan Daftar Ulang) CARD -->
      <div class="card portal-status-card card-re-registration border-start-warning h-100 shadow-sm" x-show="status === 'waiting_payment_final' || status === 'settled'" x-transition>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="badge badge-warning text-white mb-3 bg-warning"><i class="fa-solid fa-file-invoice-dollar me-1"></i> PROSES DAFTAR ULANG</div>
            <h3 class="fw-bold text-primary-900 mb-3">Pembayaran Administrasi Daftar Ulang</h3>
            <p class="text-muted leading-relaxed mb-4">
              Selamat! Anda dapat melunasi biaya daftar ulang dengan cara mencicil atau membayar lunas. Tombol <strong>Finalisasi Daftar Ulang</strong> akan aktif setelah tagihan lunas sepenuhnya.
            </p>

            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted small">Progress Pelunasan:</span>
                <span class="fw-bold text-primary-900 small">Rp {{ number_format($paidAmount, 0, ',', '.') }} / Rp {{ number_format($totalAmount, 0, ',', '.') }} ({{ $percentPaid }}% Lunas)</span>
              </div>
              <div class="progress" style="height: 10px; border-radius: 5px;">
                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentPaid }}%" aria-valuenow="{{ $percentPaid }}" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>

            <div class="invoice-box p-3 bg-light rounded-md mb-4 border">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Sisa Tagihan:</span>
                <strong class="text-danger text-lg">Rp {{ number_format(max(0, $totalAmount - $paidAmount), 0, ',', '.') }}</strong>
              </div>
              @if($reRegInvoice && $reRegInvoice->due_date)
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Batas Akhir Pelunasan:</span>
                  <span class="fw-bold text-primary-900">{{ $reRegInvoice->due_date->translatedFormat('d F Y') }}</span>
                </div>
              @endif
            </div>
          </div>

          <div class="d-flex gap-2">
            @if ($enrollment)
              <a href="{{ route('portal.re-registration.show', $enrollment->id) }}" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-upload me-2"></i> {{ $statusKey === 'settled' ? 'Lihat Rincian / Lunas' : 'Upload Bukti Cicilan' }}</a>
            @endif
          </div>
        </div>
      </div>

      <!-- 7. REJECTED (Seleksi Gagal) CARD -->
      <div class="card portal-status-card card-rejected border-start-danger h-100 shadow-sm" x-show="status === 'rejected'" x-transition>
        <div class="card-body p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="badge badge-danger mb-3 bg-danger text-white"><i class="fa-solid fa-circle-xmark me-1"></i> SELEKSI SELESAI</div>
            <h3 class="fw-bold text-danger mb-3">Mohon Maaf, Anda Belum Lulus Seleksi</h3>
            <p class="text-muted leading-relaxed mb-4">
              Terima kasih atas minat dan partisipasi Anda dalam pendaftaran SPMB {{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }}. Hasil keputusan pleno menyatakan bahwa berkas/nilai seleksi Anda <strong>BELUM MEMENUHI BATAS MINIMUM</strong> untuk jalur ini.
            </p>

            @php
              $rejectedLog = $enrollment ? $enrollment->statusLogs()->where('to_status', 'rejected')->latest('changed_at')->first() : null;
            @endphp
            <div class="alert alert-danger d-flex align-items-center mb-4 bg-danger-subtle border-danger-subtle text-danger">
              <i class="fa-solid fa-circle-info me-3 fs-4 text-danger"></i>
              <div>
                <strong>Catatan Panitia:</strong> {{ $rejectedLog && $rejectedLog->reason ? $rejectedLog->reason : 'Skor kelulusan belum memenuhi standar minimum jalur pendaftaran yang dipilih.' }}
              </div>
            </div>

            <p class="text-muted small">
              Jangan berkecil hati! Anda masih berkesempatan mendaftar kembali di jalur lain yang saat ini masih aktif. Data diri dasar Anda tidak akan hilang dan akan disalin secara otomatis.
            </p>
          </div>

          <div>
            <a href="{{ route('portal.enrollment.reapply-select') }}" class="btn btn-outline-danger px-4 py-2"><i class="fa-solid fa-rotate-right me-2"></i> Daftar Jalur Lain (Re-Apply)</a>
          </div>
        </div>
      </div>

      <!-- 8. PERMANENT STUDENT (Siswa Baru Tetap) CARD -->
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
                <strong class="text-primary-900 text-lg">{{ $enrollment->enrollment_number ?? '' }}</strong>
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

    </div>

    <!-- RIGHT COLUMN: SIDEBAR (PROFILE & RECENT ACTIVITIES) -->
    <div class="col-lg-4" data-aos="fade-left" data-aos-duration="600">

      <!-- APPLICANT PROFILE SUMMARY -->
      <div class="card mb-4 shadow-sm">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3 text-primary-800 text-uppercase tracking-wider"><i class="fa-regular fa-id-badge me-2"></i> Profil Calon Siswa</h6>

          <div class="d-flex align-items-center mb-3">
            <div class="portal-avatar me-3" style="width: 52px; height: 52px; font-size: 1.4rem;">{{ $initials }}</div>
            <div>
              <h6 class="fw-bold text-primary-900 mb-0">{{ $applicant->full_name }}</h6>
              <small class="text-muted">Jalur: {{ $enrollment && $enrollment->spmbTrack && $enrollment->spmbTrack->trackType ? $enrollment->spmbTrack->trackType->name : 'Belum Memilih' }}</small>
            </div>
          </div>

          <div class="border-top pt-3">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted small">NISN:</span>
              <span class="fw-bold text-primary-900 small">{{ $applicant->nisn ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted small">No. HP/WA:</span>
              <span class="fw-bold text-primary-900 small">{{ $applicant->phone ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted small">Asal Sekolah:</span>
              <span class="fw-bold text-primary-900 small">{{ $asalSekolah }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- RECENT TIMELINE LOGS -->
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3 text-primary-800 text-uppercase tracking-wider"><i class="fa-solid fa-clock-rotate-left me-2"></i> Log Aktivitas Terbaru</h6>

          <div class="activity-log-scrolled">
            <div class="portal-timeline">
              @forelse($statusLogs as $log)
                @php
                  // Localize to-status
                  $logTitle = match($log->to_status) {
                      'draft' => 'Formulir Pendaftaran Draf Dibuat',
                      'waiting_payment_reg' => 'Menunggu Pembayaran Formulir',
                      'registered' => 'Bukti Bayar Diunggah, Menunggu Verifikasi',
                      'verified_reg' => 'Pembayaran Registrasi Terverifikasi',
                      'in_review' => 'Proses Penilaian CBT & Berkas',
                      'passed' => 'Dinyatakan Lulus Seleksi',
                      'waiting_list' => 'Status Cadangan (Waiting List)',
                      'rejected' => 'Seleksi Jalur: Tidak Lulus',
                      'waiting_payment_final' => 'Uang Pangkal/Daftar Ulang Diterbitkan',
                      'settled' => 'Biaya Daftar Ulang Lunas',
                      'permanent_student' => 'Siswa Baru Tetap Terverifikasi',
                      default => 'Pembaruan Status Pendaftaran',
                  };
                  $dotColor = match($log->to_status) {
                      'draft' => 'bg-secondary',
                      'waiting_payment_reg', 'waiting_payment_final' => 'bg-warning',
                      'registered', 'in_review' => 'bg-info',
                      'verified_reg', 'passed', 'settled', 'permanent_student' => 'bg-success',
                      'rejected' => 'bg-danger',
                      default => 'bg-primary',
                  };
                @endphp
                <div class="portal-timeline-item active">
                  <div class="timeline-dot {{ $dotColor }}"></div>
                  <div class="timeline-content">
                    <div class="timeline-time">{{ $log->changed_at->diffForHumans() }} ({{ $log->changed_at->format('H:i') }} WIB)</div>
                    <h6 class="fw-bold text-primary-900 mb-1">{{ $logTitle }}</h6>
                    @if($log->reason)
                      <p class="text-muted small mb-0">Catatan: {{ $log->reason }}</p>
                    @endif
                  </div>
                </div>
              @empty
                <!-- Default item since every applicant has at least their user registration date -->
                <div class="portal-timeline-item active">
                  <div class="timeline-dot bg-success"></div>
                  <div class="timeline-content">
                    <div class="timeline-time">{{ $user->created_at->diffForHumans() }}</div>
                    <h6 class="fw-bold text-primary-900 mb-1">Registrasi Akun Calon Siswa Berhasil</h6>
                    <p class="text-muted small mb-0">Akun pendaftaran mandiri Anda berhasil didaftarkan di sistem.</p>
                  </div>
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>

</div>
@endsection

@push('js')
<script>
  function dashboardApp() {
    return {
      status: localStorage.getItem('spmb_simulated_status') || '{{ $statusKey }}',
      showConfetti: false,

      init() {
        if (this.status === 'permanent_student') {
          this.triggerConfetti();
        }
      },

      get isSimulated() {
        return localStorage.getItem('spmb_simulated_status') !== null;
      },

      get activeStep() {
        switch (this.status) {
          case 'no_enrollment': return 1;
          case 'draft': return 1;
          case 'waiting_payment_reg': return 2;
          case 'registered': return 2;
          case 'verified_reg': return 3;
          case 'in_review': return 3;
          case 'waiting_list': return 3;
          case 'passed': return 4;
          case 'rejected': return 4;
          case 'waiting_payment_final': return 5;
          case 'settled': return 5;
          case 'permanent_student': return 6;
          default: return 1;
        }
      },

      setStatus(newStatus) {
        this.status = newStatus;
        localStorage.setItem('spmb_simulated_status', newStatus);
        if (newStatus === 'permanent_student') {
          this.triggerConfetti();
        } else {
          this.showConfetti = false;
        }
      },

      resetToDb() {
        localStorage.removeItem('spmb_simulated_status');
        this.status = '{{ $statusKey }}';
        if (this.status === 'permanent_student') {
          this.triggerConfetti();
        } else {
          this.showConfetti = false;
        }
        toastr.success('Status dashboard dikembalikan ke kondisi database.');
      },

      triggerConfetti() {
        this.showConfetti = true;
        setTimeout(() => {
          this.showConfetti = false;
        }, 6000);
      },

      getRandomColor() {
        const colors = ['#f2c94c', '#0b4a6f', '#28a745', '#e53935', '#0077cc', '#a855f7', '#ec4899'];
        return colors[Math.floor(Math.random() * colors.length)];
      }
    }
  }
</script>
@endpush
