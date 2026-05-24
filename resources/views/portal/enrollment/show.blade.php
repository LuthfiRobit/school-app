@extends('portal.layouts.app')

@section('title', 'Ringkasan Pendaftaran')

@push('css')
<style>
  /* Custom style overrides for summary tables and files */
  .summary-label {
    font-size: 0.8rem;
    color: var(--color-text-muted);
    margin-bottom: 0.15rem;
  }
  .summary-value {
    font-weight: 600;
    color: var(--color-primary-950);
    font-size: 0.9rem;
  }
  .summary-section-card {
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-divider);
    background-color: white;
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
  }
  .summary-section-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--color-primary-300);
  }
  .summary-file-item {
    border: 1px solid var(--color-divider);
    border-radius: var(--radius-md);
    padding: 0.5rem 0.75rem;
    background-color: var(--color-surface);
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
  }
  .summary-file-item:last-child {
    margin-bottom: 0;
  }
  .receipt-header-logo {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .receipt-header-logo img {
    height: 32px;
    width: auto;
  }
  .print-only {
    display: none !important;
  }
  
  /* Print media styling */
  @media print {
    body {
      background-color: white !important;
      background-image: none !important;
    }
    .portal-navbar, .portal-simulator-widget, .breadcrumb, .no-print, .btn, .alert-action-box, header, footer {
      display: none !important;
    }
    .print-only {
      display: block !important;
    }
    main {
      padding: 0 !important;
      margin: 0 !important;
      max-width: 100% !important;
    }
    .col-lg-8, .col-lg-4 {
      width: 100% !important;
      flex: none !important;
    }
    .card {
      border: none !important;
      box-shadow: none !important;
    }
    .summary-section-card {
      border: 1px solid #ddd !important;
      box-shadow: none !important;
      page-break-inside: avoid;
    }
  }
</style>
@endpush

@section('content')
@php
  $applicant = Auth::user()->applicant;
  $registrationInvoice = $enrollment->invoices()->where('category', 'registration')->first();
  $schoolIdentity = \App\Models\SchoolIdentity::first();
@endphp

<div class="w-100">

  <!-- PRINT ONLY INSTITUTION HEADER -->
  <div class="print-only w-100 border-bottom pb-3 mb-4">
    <div class="d-flex align-items-center justify-content-between">
      <div class="receipt-header-logo">
        <img src="{{ $schoolIdentity && $schoolIdentity->logo ? asset('storage/' . $schoolIdentity->logo) : asset('template/landing/assets/img/logo/logo-sma.png') }}" alt="Logo">
        <div>
          <h4 class="fw-bold text-primary-900 m-0">{{ $schoolIdentity->school_name ?? 'SMA TUNAS LUHUR' }}</h4>
          <span class="text-muted small">Panitia Penerimaan Siswa Baru (SPMB)</span>
        </div>
      </div>
      <div class="text-end">
        <strong class="text-primary-950 d-block text-lg">{{ $enrollment->enrollment_number ?? 'Belum Terdaftar' }}</strong>
        <span class="text-muted small">Kartu Tanda Bukti Registrasi</span>
      </div>
    </div>
  </div>

  <!-- Page Header & Breadcrumb -->
  <div class="mb-4 no-print" data-aos="fade-up" data-aos-duration="600">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2 small">
        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}" class="text-primary-600 text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ringkasan Berkas</li>
      </ol>
    </nav>
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
      <div>
        <h4 class="fw-bold text-primary-900 mb-1">Ringkasan Pendaftaran Anda</h4>
        <p class="text-muted mb-0">Tinjau kembali berkas pendaftaran Anda yang telah terkirim ke server panitia seleksi.</p>
      </div>
      <div class="mt-3 mt-md-0 d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary px-3 py-2 border-primary text-primary-600" onclick="window.print()">
          <i class="fa-solid fa-print me-1"></i> Cetak Kartu Registrasi
        </button>
        <a href="{{ route('portal.dashboard') }}" class="btn btn-sm btn-primary px-3 py-2">
          Masuk ke Dashboard <i class="fa-solid fa-chevron-right ms-1 fs-xs"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- SUCCESS ALERT CALLOUT -->
  <div class="card summary-section-card border-start-success p-3 mb-4" data-aos="fade-up" data-aos-duration="600">
    <div class="d-flex align-items-start">
      <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px;">
        <i class="fa-solid fa-circle-check fs-5"></i>
      </div>
      <div class="flex-grow-1">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-2">
          <div>
            <h5 class="fw-bold text-success mb-1">Pendaftaran Berhasil Dikirim!</h5>
            <p class="text-muted small mb-0">Berkas Anda telah tersimpan secara permanen dalam sistem antrean SPMB {{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }}.</p>
          </div>
          <div class="mt-2 mt-md-0 text-md-end">
            <span class="text-muted small d-block">Nomor Pendaftaran:</span>
            <strong class="text-primary-950 text-xl">{{ $enrollment->enrollment_number ?? 'Proses Generate...' }}</strong>
          </div>
        </div>
        
        <div class="border-top pt-3 mt-2 no-print d-flex flex-wrap gap-3">
          <span class="small text-muted">
            <i class="fa-solid fa-clock me-1 text-primary-500"></i> Status: 
            <span class="badge {{ $enrollment->status->badgeClass() }}">{{ $enrollment->status->label() }}</span>
          </span>
          <span class="small text-muted">
            <i class="fa-solid fa-calendar me-1 text-primary-500"></i> Tanggal Submit: 
            <strong class="text-primary-850">{{ $enrollment->created_at->translatedFormat('d F Y H:i') }} WIB</strong>
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Layout Grid -->
  <div class="row g-4">

    <!-- LEFT COLUMN: DETAILED SUMMARY TABLES -->
    <div class="col-lg-8" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
      
      <!-- SECTION 1: BIODATA -->
      <div class="card summary-section-card p-4">
        <h6 class="fw-bold text-primary-800 text-uppercase tracking-wider border-bottom pb-2 mb-3">
          <i class="fa-regular fa-address-card me-2 text-primary-600"></i> Profil &amp; Identitas Calon Siswa
        </h6>
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="summary-label">Nama Lengkap:</div>
            <div class="summary-value">{{ $applicant->full_name }}</div>
          </div>
          <div class="col-sm-6">
            <div class="summary-label">NISN (Nomor Induk Siswa Nasional):</div>
            <div class="summary-value">{{ $applicant->nisn }}</div>
          </div>
          <div class="col-sm-6">
            <div class="summary-label">No. Telepon / WhatsApp:</div>
            <div class="summary-value">{{ $applicant->phone }}</div>
          </div>
          <div class="col-sm-6">
            <div class="summary-label">Jenis Kelamin:</div>
            <div class="summary-value">{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
          </div>
          <div class="col-sm-6">
            <div class="summary-label">Tempat, Tanggal Lahir:</div>
            <div class="summary-value">{{ $applicant->place_of_birth }}, {{ $applicant->date_of_birth ? $applicant->date_of_birth->translatedFormat('d F Y') : '' }}</div>
          </div>
          <div class="col-sm-6">
            <div class="summary-label">Agama:</div>
            <div class="summary-value">{{ $applicant->religion }}</div>
          </div>
        </div>
      </div>

      <!-- SECTION 2: DYNAMIC FIELD DETAILS -->
      @php
        $nonFileFields = $formData->filter(fn($data) => $data->field->field_type !== 'file');
      @endphp
      @if($nonFileFields->count() > 0)
        <div class="card summary-section-card p-4">
          <h6 class="fw-bold text-primary-800 text-uppercase tracking-wider border-bottom pb-2 mb-3">
            <i class="fa-solid fa-sliders me-2 text-primary-600"></i> Detail Formulir Jalur: <span class="text-primary-700">{{ $enrollment->spmbTrack->trackType->name }}</span>
          </h6>
          <div class="row g-3">
            @foreach($nonFileFields as $data)
              <div class="col-sm-6">
                <div class="summary-label">{{ $data->field->field_label }}:</div>
                <div class="summary-value">{{ $data->value }}</div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      <!-- SECTION 3: UPLOADED FILE LISTS -->
      @php
        $fileFields = $formData->filter(fn($data) => $data->field->field_type === 'file');
      @endphp
      @if($fileFields->count() > 0)
        <div class="card summary-section-card p-4">
          <h6 class="fw-bold text-primary-800 text-uppercase tracking-wider border-bottom pb-2 mb-3">
            <i class="fa-solid fa-folder-closed me-2 text-primary-600"></i> Dokumen Lampiran Pendaftaran
          </h6>
          <div class="d-flex flex-column gap-2">
            @foreach($fileFields as $data)
              <div class="summary-file-item">
                <div class="d-flex align-items-center">
                  <div class="rounded bg-primary bg-opacity-10 text-primary-700 d-flex align-items-center justify-content-center me-3" style="width:36px;height:36px;font-size:1.1rem;">
                    <i class="fa-solid fa-file-pdf"></i>
                  </div>
                  <div>
                    <strong class="text-primary-900 small d-block">{{ basename($data->value) }}</strong>
                    <span class="text-muted text-xxs d-block">{{ $data->field->field_label }}</span>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <a href="{{ asset('storage/' . $data->value) }}" target="_blank" class="btn btn-xs btn-outline-primary px-2 py-1">
                    <i class="fa-solid fa-eye me-1"></i> Lihat
                  </a>
                  <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small"><i class="fa-solid fa-circle-check me-1"></i> Terunggah</span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

    </div>

    <!-- RIGHT COLUMN: SIDEBAR CALLOUTS -->
    <div class="col-lg-4 no-print" data-aos="fade-left" data-aos-duration="600" data-aos-delay="150">
      
      @if($registrationInvoice)
        <!-- STEPPING ACTION PANEL -->
        <div class="card mb-4 shadow-sm border-0 border-start-accent">
          <div class="card-body p-4">
            <h6 class="fw-bold text-primary-800 text-uppercase tracking-wider mb-3"><i class="fa-solid fa-arrow-right-to-bracket me-2 text-primary-600"></i> Langkah Selanjutnya</h6>
            
            @if($registrationInvoice->status->value === 'paid')
              <div class="invoice-box p-3 rounded-md mb-4 border bg-success bg-opacity-10 text-center">
                <span class="text-muted small d-block mb-1">Status Pembayaran:</span>
                <strong class="text-success text-xl d-block mb-2">Rp {{ number_format($registrationInvoice->total_amount, 0, ',', '.') }}</strong>
                <span class="badge bg-success text-white small"><i class="fa-solid fa-circle-check me-1"></i> Lunas</span>
              </div>
              <p class="text-muted small mb-0">Biaya pendaftaran formulir Anda telah lunas. Panitia sedang memverifikasi berkas Anda. Pantau dashboard secara berkala.</p>
            @else
              <p class="text-muted small">Untuk memfinalisasi berkas pendaftaran Anda masuk ke antrean verifikasi berkas, mohon lakukan pelunasan <strong>Biaya Registrasi Formulir</strong> berikut:</p>
              
              <div class="invoice-box p-3 rounded-md mb-4 border bg-light text-center">
                <span class="text-muted small d-block mb-1">Total Tagihan Formulir:</span>
                <strong class="text-error text-xl d-block mb-2">Rp {{ number_format($registrationInvoice->total_amount, 0, ',', '.') }}</strong>
                <span class="badge {{ $registrationInvoice->status->badgeClass() }} small">{{ $registrationInvoice->status->label() }}</span>
              </div>

              <div class="d-grid gap-2">
                <a href="{{ route('portal.payment.show', $enrollment->id) }}" class="btn btn-success text-white py-2 fw-bold">
                  <i class="fa-solid fa-file-invoice-dollar me-2"></i> Unggah Bukti Transfer
                </a>
                <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary py-2 border-primary text-primary-600">
                  Kembali ke Dashboard
                </a>
              </div>
            @endif
          </div>
        </div>
      @else
        <!-- NO FEE INFO PANEL -->
        <div class="card mb-4 shadow-sm border-0 border-start-accent">
          <div class="card-body p-4">
            <h6 class="fw-bold text-primary-800 text-uppercase tracking-wider mb-3"><i class="fa-solid fa-circle-info me-2 text-primary-600"></i> Informasi Pendaftaran</h6>
            <div class="alert alert-success p-3 small mb-3">
              <i class="fa-solid fa-circle-check me-1"></i> Jalur ini gratis (tidak dipungut biaya pendaftaran). Berkas Anda otomatis masuk ke tahap verifikasi berkas.
            </div>
            <a href="{{ route('portal.dashboard') }}" class="btn btn-primary w-100 py-2">
              Ke Dashboard Saya
            </a>
          </div>
        </div>
      @endif

    </div>

  </div>

</div>
@endsection
