@extends('portal.layouts.app')

@section('title', 'Daftar Ulang')

@push('css')
<style>
  .drag-drop-area {
    border-color: var(--color-divider);
    background-color: var(--color-surface);
  }
  .drag-drop-area:hover {
    border-color: var(--color-primary-400);
    background-color: white;
  }
  .hover-shadow-sm:hover {
    box-shadow: var(--shadow-sm);
    border-color: var(--color-primary-300) !important;
  }
  .transition-all {
    transition: all 0.2s ease-in-out;
  }
  .pulse-success {
    animation: pulse-success-animation 2s infinite;
  }
  @keyframes pulse-success-animation {
    0% {
      box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4);
    }
    70% {
      box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
      box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
  }
</style>
@endpush

@section('content')
<div class="w-100">
  
  <!-- Page Header & Breadcrumb -->
  <div class="mb-4" data-aos="fade-up" data-aos-duration="600">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2 small">
        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}" class="text-primary-600 text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Ulang</li>
      </ol>
    </nav>
    
    <div>
      <h4 class="fw-bold text-primary-900 mb-1">Daftar Ulang & Administrasi Siswa Baru</h4>
      <p class="text-muted mb-0">Lakukan pelunasan biaya uang pangkal pendaftaran untuk finalisasi status siswa tetap.</p>
    </div>
  </div>

  @if($enrollment->status->value === 'permanent_student')
    <!-- TAMPILAN SUKSES SISWA TETAP -->
    <div class="card border-0 shadow-sm overflow-hidden mb-4" data-aos="fade-up" data-aos-duration="600">
      <div class="p-5 text-center bg-gradient-success text-white position-relative" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
        <!-- Pattern background overlay -->
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-5" style="background-image: radial-gradient(circle, #fff 10%, transparent 11%), radial-gradient(circle, #fff 10%, transparent 11%); background-size: 20px 20px; background-position: 0 0, 10px 10px;"></div>
        
        <div class="position-relative z-1">
          <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center mx-auto mb-4 shadow" style="width: 80px; height: 80px; font-size: 2.5rem;">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <h2 class="fw-bold mb-2">Selamat, Daftar Ulang Anda Selesai!</h2>
          <p class="mb-4 leading-relaxed mx-auto" style="max-width: 600px;">
            Halo <strong>{{ $enrollment->applicant->full_name }}</strong>, Anda telah resmi terdaftar sebagai Siswa Baru Tetap di <strong>{{ $schoolIdentity->school_name ?? 'SMA Tunas Luhur' }}</strong>. Silakan unduh Surat Pernyataan resmi Anda di bawah ini.
          </p>
          <a href="{{ route('portal.letter.download', $enrollment->id) }}" class="btn btn-light text-success fw-bold px-4 py-2.5 shadow-sm hover-shadow-md">
            <i class="fa-solid fa-file-pdf me-2"></i> Unduh Surat Pernyataan Siswa Tetap
          </a>
        </div>
      </div>
      
      <div class="card-body p-4 bg-white">
        <h5 class="fw-bold text-primary-900 mb-3 border-bottom pb-2">Status Pembayaran Uang Pangkal</h5>
        <div class="row g-3">
          <div class="col-sm-4 text-center border-end">
            <span class="text-muted small d-block">Total Tagihan</span>
            <strong class="text-primary-950 fs-5">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
          </div>
          <div class="col-sm-4 text-center border-end">
            <span class="text-muted small d-block">Jumlah Dibayar</span>
            <strong class="text-success fs-5">Rp {{ number_format($paidAmount, 0, ',', '.') }}</strong>
          </div>
          <div class="col-sm-4 text-center">
            <span class="text-muted small d-block">Status Invoice</span>
            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-2.5 py-1 mt-1 small">LUNAS</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Riwayat Pembayaran untuk Siswa Tetap -->
    <div class="row" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
      <div class="col-12">
        @include('portal.re-registration.partials.payment-history')
      </div>
    </div>

  @else
    <!-- TAMPILAN PROSES PEMBAYARAN CICILAN / FINALISASI -->
    <div class="row g-4">
      <!-- Left Column: Invoice Details & Bank Accounts -->
      <div class="col-lg-7" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
        @include('portal.re-registration.partials.invoice-detail')
        @include('portal.re-registration.partials.bank-accounts')
      </div>

      <!-- Right Column: Progress, Upload Form, and Payment History -->
      <div class="col-lg-5" data-aos="fade-up" data-aos-duration="600" data-aos-delay="150">
        @include('portal.re-registration.partials.payment-progress')
        
        @if($enrollment->status->value !== 'settled')
          @include('portal.re-registration.partials.upload-form')
        @endif
        
        @include('portal.re-registration.partials.payment-history')
      </div>
    </div>
  @endif

</div>
@endsection

@push('js')
  @include('portal.re-registration.partials.scripts')
@endpush
