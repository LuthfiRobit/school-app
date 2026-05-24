<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title') - Portal SPMB Tunas Luhur</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('template/landing/assets/img/logo/logo-sma.png') }}">
  
  <!-- FontAwesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Design System CSS -->
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/variables.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/typography.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/vendor/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/index.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/portal.css') }}">
  
  <!-- Toastr CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  
  <!-- AOS (Animate on Scroll) CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  
  @stack('css')
</head>
<body class="auth-bg">
  <div class="auth-wrapper">
    <!-- Left Brand Side (Visible on Desktop) -->
    <div class="auth-brand">
      <img src="{{ asset('template/landing/assets/img/logo/logo2.png') }}" alt="Tunas Luhur" class="brand-logo" data-aos="zoom-in" data-aos-duration="800">
      <h1 class="brand-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Pintu Masa Depan Anda</h1>
      <p class="brand-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
        Bergabunglah bersama SMA Tunas Luhur dan kembangkan potensi terbaik Anda melalui pendidikan karakter dan akademik yang unggul.
      </p>
    </div>

    <!-- Right Form Side -->
    <div class="auth-content">
      <div class="auth-card" data-aos="fade-up" data-aos-duration="600">
        <div class="text-center mb-4 d-lg-none">
          <a href="/">
            <img src="{{ asset('template/landing/assets/img/logo/logo2.png') }}" alt="Logo Tunas Luhur" style="height: 48px; margin-bottom: 1rem;">
          </a>
        </div>
        
        @yield('content')
      </div>
    </div>
  </div>

  <!-- JQuery & Bootstrap -->
  <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
  <script src="{{ asset('template/landing/assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
  
  <!-- Toastr JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  
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
  </script>
  @stack('js')
</body>
</html>
