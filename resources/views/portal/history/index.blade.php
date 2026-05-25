@extends('portal.layouts.app')

@section('title', 'Riwayat Pendaftaran')

@section('content')
<div class="w-100">
  
  <!-- Page Header & Breadcrumb -->
  <div class="mb-4" data-aos="fade-up" data-aos-duration="600">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2 small">
        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}" class="text-primary-600 text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Riwayat Pendaftaran</li>
      </ol>
    </nav>
    
    <div>
      <h4 class="fw-bold text-primary-900 mb-1">Riwayat Pendaftaran Anda</h4>
      <p class="text-muted mb-0">Lihat semua status, jalur, dan tahun ajaran pendaftaran yang pernah Anda coba.</p>
    </div>
  </div>

  <!-- TABLE CARD -->
  <div class="card shadow-sm border-0" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
    <div class="card-body p-4">
      
      @if($enrollments->isEmpty())
        <div class="text-center py-5">
          <div class="text-muted mb-3" style="font-size: 3rem;"><i class="fa-solid fa-folder-open text-primary-300"></i></div>
          <h5 class="fw-bold text-primary-900 mb-1">Belum ada riwayat pendaftaran</h5>
          <p class="text-muted small mb-4">Anda belum memiliki pendaftaran aktif maupun lampau.</p>
          <a href="{{ route('portal.enrollment.select-track') }}" class="btn btn-primary px-4 py-2">
            <i class="fa-solid fa-file-signature me-2"></i> Mulai Pendaftaran Baru
          </a>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr class="table-light">
                <th class="text-muted small ps-3 py-3" style="width: 20%;">Tahun Ajaran</th>
                <th class="text-muted small py-3" style="width: 25%;">Jalur Seleksi</th>
                <th class="text-muted small py-3" style="width: 20%;">Tanggal Daftar</th>
                <th class="text-muted small text-center py-3" style="width: 20%;">Status</th>
                <th class="text-muted small text-center pe-3 py-3" style="width: 15%;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($enrollments as $enrollment)
                @php
                  // Tentukan link rute berdasarkan status pendaftaran
                  $actionUrl = match($enrollment->status->value) {
                      'draft' => route('portal.enrollment.form', $enrollment->spmb_track_id),
                      'waiting_payment_reg' => route('portal.payment.show', $enrollment->id),
                      'passed', 'waiting_payment_final', 'settled', 'permanent_student' => route('portal.re-registration.show', $enrollment->id),
                      default => route('portal.enrollment.show', $enrollment->id),
                  };

                  $actionLabel = match($enrollment->status->value) {
                      'draft' => 'Lengkapi Form',
                      'waiting_payment_reg' => 'Bayar Pendaftaran',
                      'passed' => 'Daftar Ulang',
                      'waiting_payment_final', 'settled' => 'Cicilan / Finalisasi',
                      'permanent_student' => 'Unduh Surat',
                      default => 'Lihat Detail',
                  };

                  $btnClass = match($enrollment->status->value) {
                      'draft' => 'btn-outline-primary border-primary text-primary-600',
                      'waiting_payment_reg' => 'btn-warning text-dark',
                      'passed', 'waiting_payment_final', 'settled' => 'btn-success text-white',
                      'permanent_student' => 'btn-primary text-white',
                      default => 'btn-light-secondary text-primary-900 border',
                  };
                @endphp
                <tr>
                  <td class="ps-3 py-3">
                    <strong class="text-primary-950 d-block">
                      {{ $enrollment->spmbTrack->spmbConfiguration->academicYear->year ?? 'Tahun Pelajaran' }}
                    </strong>
                    <span class="text-muted text-xxs">Periode SPMB</span>
                  </td>
                  <td class="py-3">
                    <strong class="text-primary-900 d-block">
                      {{ $enrollment->spmbTrack->trackType->name ?? 'Jalur' }}
                    </strong>
                    <span class="text-muted text-xxs text-monospace">No. Reg: {{ $enrollment->enrollment_number ?? '-' }}</span>
                  </td>
                  <td class="py-3 text-muted small">
                    {{ $enrollment->created_at->translatedFormat('d M Y') }}
                    <span class="d-block text-xxs text-muted">{{ $enrollment->created_at->format('H:i') }} WIB</span>
                  </td>
                  <td class="text-center py-3">
                    <span class="badge {{ $enrollment->status->badgeClass() }} px-3 py-1.5 small">
                      {{ $enrollment->status->label() }}
                    </span>
                  </td>
                  <td class="text-center pe-3 py-3">
                    <a href="{{ $actionUrl }}" class="btn btn-sm {{ $btnClass }} fw-bold px-3 py-1.5 transition-all duration-200">
                      {{ $actionLabel }}
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif

    </div>
  </div>

</div>
@endsection
