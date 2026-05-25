@extends('portal.layouts.app')

@section('title', 'Pembayaran Pendaftaran')

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
</style>
@endpush

@section('content')
<div class="w-100">
  
  <!-- Page Header & Breadcrumb -->
  <div class="mb-4" data-aos="fade-up" data-aos-duration="600">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2 small">
        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}" class="text-primary-600 text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pembayaran Pendaftaran</li>
      </ol>
    </nav>
    
    <div>
      <h4 class="fw-bold text-primary-900 mb-1">Pembayaran Biaya Formulir</h4>
      <p class="text-muted mb-0">Selesaikan pelunasan biaya registrasi untuk memverifikasi pendaftaran Anda.</p>
    </div>
  </div>

  <div class="row g-4">
    <!-- Left Column: Invoice Details & Bank Accounts -->
    <div class="col-lg-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
      @include('portal.payment.partials.invoice-detail')
    </div>

    <!-- Right Column: Upload Form & Payment History -->
    <div class="col-lg-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="150">
      @include('portal.payment.partials.upload-form')
      @include('portal.payment.partials.payment-history')
    </div>
  </div>
</div>
@endsection

@push('js')
  @include('portal.payment.partials.scripts')
@endpush
