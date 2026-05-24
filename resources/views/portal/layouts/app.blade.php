<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') - Portal SPMB Tunas Luhur</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('template/landing/assets/img/logo/logo-sma.png') }}">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Design System CSS -->
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/variables.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/typography.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/vendor/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/index.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/portal.css') }}">

  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

  <!-- AOS (Animate on Scroll) CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <style>
    .swal2-container {
      z-index: 9999 !important;
    }
  </style>

  @stack('css')
</head>

@php
    $applicant = Auth::user()->applicant;
    $activeEnrollment = $applicant?->enrollments()
        ->where('status', '!=', \App\Enums\EnrollmentStatus::REJECTED)
        ->first();
    $schoolIdentity = \App\Models\SchoolIdentity::first();
    $helpPhone = $schoolIdentity?->whatsapp ?? '6281234567890';
    $initials = strtoupper(substr(Auth::user()->name, 0, 2));
@endphp

<body class="portal-bg" x-data="{ mobileMenuOpen: false }">

  <!-- TOP NAVBAR -->
  <header class="portal-navbar">
    <div class="container-xxl d-flex align-items-center justify-content-between h-100">

      <!-- Brand Logo -->
      <a href="/" class="portal-navbar-brand">
        <img src="{{ $schoolIdentity && $schoolIdentity->logo ? asset('storage/' . $schoolIdentity->logo) : asset('template/landing/assets/img/logo/logo-sma.png') }}" alt="School Logo">
        <div>
          <div class="portal-brand-title">{{ $schoolIdentity->school_name ?? 'SPMB Tunas Luhur' }}</div>
          <div class="portal-brand-tagline">Portal Calon Siswa</div>
        </div>
      </a>

      <!-- Desktop Links -->
      <nav class="portal-nav-links d-none d-md-flex align-items-center" aria-label="Navigasi portal">
        <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-line me-1"></i> Dashboard</a>
        <a href="{{ route('portal.history.index') }}" class="{{ request()->routeIs('portal.history.index') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left me-1"></i> Riwayat</a>
        <a href="https://wa.me/{{ $helpPhone }}" target="_blank"><i class="fa-solid fa-headset me-1"></i> Bantuan</a>
      </nav>

      <!-- Profile Dropdown (Right) -->
      <div class="portal-navbar-profile position-relative" x-data="{ openProfile: false }" @click.outside="openProfile = false">
        <button class="portal-profile-trigger d-flex align-items-center" @click="openProfile = !openProfile" aria-haspopup="true" :aria-expanded="openProfile">
          <div class="portal-avatar">{{ $initials }}</div>
          <span class="portal-username d-none d-sm-inline">{{ Auth::user()->name }}</span>
          <i class="fa-solid fa-chevron-down ms-2 fs-xs"></i>
        </button>

        <!-- Dropdown Menu -->
        <div class="portal-dropdown-menu" x-show="openProfile" x-transition:enter="transition ease-out duration-100"
          x-transition:enter-start="opacity-0 transform scale-95"
          x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
          <div class="dropdown-header border-bottom pb-2 mb-1">
            <h6 class="mb-0 fw-bold text-primary-900">{{ Auth::user()->name }}</h6>
            <small class="text-muted">No. Reg: {{ $activeEnrollment->enrollment_number ?? 'Belum Terdaftar' }}</small>
          </div>
          <div class="dropdown-divider"></div>
          <form action="{{ route('applicant.logout') }}" method="POST" id="logout-form">
            @csrf
            <a href="javascript:void(0)" class="portal-dropdown-item text-error" onclick="document.getElementById('logout-form').submit();">
              <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar
            </a>
          </form>
        </div>
      </div>

      <!-- Hamburger Menu Toggle (Mobile) -->
      <button class="portal-nav-toggle d-md-none" id="portalNavToggle" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Buka navigasi mobile">
        <span></span>
        <span></span>
        <span></span>
      </button>

    </div>
  </header>

  <!-- Mobile Drawer Navigation -->
  <div class="portal-mobile-drawer d-md-none" :class="{ 'open': mobileMenuOpen }" style="display: none;" x-show="mobileMenuOpen" @click.outside="mobileMenuOpen = false" x-transition>
    <nav class="d-flex flex-column p-4" aria-label="Navigasi mobile portal">
      <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'active' : '' }} py-3 border-bottom"><i class="fa-solid fa-chart-line me-2 text-primary-600"></i> Dashboard</a>
      <a href="{{ route('portal.history.index') }}" class="{{ request()->routeIs('portal.history.index') ? 'active' : '' }} py-3 border-bottom"><i class="fa-solid fa-clock-rotate-left me-2 text-primary-600"></i> Riwayat Pendaftaran</a>
      <a href="https://wa.me/{{ $helpPhone }}" target="_blank" class="py-3 border-bottom"><i class="fa-solid fa-headset me-2 text-primary-600"></i> Hubungi Bantuan</a>
      <a href="javascript:void(0)" class="py-3 text-error" onclick="document.getElementById('logout-form').submit();"><i class="fa-solid fa-right-from-bracket me-2"></i> Keluar Akun</a>
    </nav>
  </div>

  <!-- MAIN CONTAINER -->
  <main class="container-xxl py-4 py-lg-5 portal-content">
      @yield('content')
  </main>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
  <script src="{{ asset('template/landing/assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
  
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Axios JS -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
      axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
      axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  </script>

  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <!-- AOS Animation Script -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ once: true });

    $(document).ready(function() {
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif
        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
    });

    /**
     * Helper to init Select2 inside Alpine.js
     */
    function initSelect2(el, field, self) {
        $(el).select2({
            theme: 'bootstrap-5',
            placeholder: $(el).data('placeholder'),
            allowClear: true,
            width: '100%'
        }).on('change', function() {
            self.formData[field] = $(this).val();
        });

        if (self.formData[field]) {
            $(el).val(self.formData[field]).trigger('change.select2');
        }
    }
  </script>

  @stack('js')
</body>
</html>
