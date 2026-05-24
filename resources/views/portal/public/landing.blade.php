<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $schoolIdentity->school_name ?? 'SPMB Tunas Luhur' }} - Portal Pendaftaran Siswa</title>
  <meta name="description" content="Portal pendaftaran calon siswa Sistem Penerimaan Siswa Baru (SPMB) {{ $schoolIdentity->school_name ?? 'Tunas Luhur' }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ $schoolIdentity && $schoolIdentity->logo ? asset('storage/' . $schoolIdentity->logo) : asset('template/landing/assets/img/logo/logo-sma.png') }}">

  <!-- SEO Open Graph Metadata -->
  <meta property="og:title" content="{{ $schoolIdentity->school_name ?? 'SPMB Tunas Luhur' }} - Portal Pendaftaran Siswa">
  <meta property="og:description"
    content="Sistem Penerimaan Siswa Baru (SPMB) {{ $schoolIdentity->school_name ?? 'Tunas Luhur' }}. Proses pendaftaran mudah, cepat, transparan, dan terpantau real-time.">
  <meta property="og:image" content="{{ $schoolIdentity && $schoolIdentity->logo ? asset('storage/' . $schoolIdentity->logo) : asset('template/landing/assets/img/logo/logo-sma.png') }}">
  <meta property="og:type" content="website">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Global Design System & Theme Variables -->
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/variables.css') }}">

  <!-- Global Typography System -->
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/typography.css') }}">

  <!-- Vendor CSS (local assets) -->
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/vendor/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/plugins/swiper-bundle.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/plugins/glightbox.min.css') }}">

  <!-- Page-Specific Styles -->
  <link rel="stylesheet" href="{{ asset('template/landing/assets/css/index.css') }}">

  <!-- AOS (Animate On Scroll) CSS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
  <header class="header" id="main-header">
    <div class="container-xxl header-inner">
      <!-- Brand -->
      <div class="brand">
        <a href="/">
          <img src="{{ $schoolIdentity && $schoolIdentity->logo ? asset('storage/' . $schoolIdentity->logo) : asset('template/landing/assets/img/logo/logo-sma.png') }}" alt="{{ $schoolIdentity->school_name ?? 'SPMB Tunas Luhur' }}">
        </a>
        <div>
          <div class="brand-name">{{ $schoolIdentity->school_name ?? 'SPMB Tunas Luhur' }}</div>
          <div class="brand-tagline">Portal Pendaftaran Siswa Baru</div>
        </div>
      </div>

      <!-- Desktop Navigation -->
      <nav class="nav-desktop" aria-label="Navigasi utama">
        <a href="#jalur-pendaftaran">Jalur</a>
        <a href="#alur-pendaftaran">Alur</a>
        <a href="#persyaratan">Persyaratan</a>
        <a href="#faq">FAQ</a>
        @auth
          @if(auth()->user()->hasRole('Applicant'))
            <a href="{{ route('portal.dashboard') }}" class="btn-base btn-primary nav-cta-btn">Dashboard Portal</a>
          @else
            <a href="{{ route('admin.dashboard') }}" class="btn-base btn-primary nav-cta-btn">Dashboard Admin</a>
          @endif
        @else
          <a href="{{ route('applicant.register') }}" class="btn-base btn-primary nav-cta-btn">Daftar Sekarang</a>
        @endauth
      </nav>

      <!-- Hamburger Toggle (Mobile) -->
      <button class="nav-toggle" id="navToggle" aria-label="Buka menu navigasi" aria-expanded="false"
        aria-controls="navMobile">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div class="nav-mobile" id="navMobile" aria-hidden="true">
      <nav aria-label="Navigasi mobile">
        <a href="#jalur-pendaftaran" class="nav-mobile-link">Jalur Pendaftaran</a>
        <a href="#alur-pendaftaran" class="nav-mobile-link">Alur Pendaftaran</a>
        <a href="#persyaratan" class="nav-mobile-link">Persyaratan Dokumen</a>
        <a href="#faq" class="nav-mobile-link">FAQ</a>
        @auth
          @if(auth()->user()->hasRole('Applicant'))
            <a href="{{ route('portal.dashboard') }}" class="btn-base btn-primary nav-mobile-cta">Dashboard Portal</a>
          @else
            <a href="{{ route('admin.dashboard') }}" class="btn-base btn-primary nav-mobile-cta">Dashboard Admin</a>
          @endif
        @else
          <a href="{{ route('applicant.register') }}" class="btn-base btn-primary nav-mobile-cta">Daftar Sekarang</a>
        @endauth
      </nav>
    </div>
  </header>

  <!-- Mobile Nav Overlay -->
  <div class="nav-overlay" id="navOverlay" aria-hidden="true"></div>

  <main>
    <!-- Hero Section -->
    <section class="hero">
      <div class="container-xxl">
        <div style="display:flex;align-items:center;gap:28px;flex-wrap:wrap">
          <div style="flex:1;min-width:300px;padding:20px 0" data-aos="fade-right" data-aos-duration="1000">
            <span
              style="background:rgba(242,201,76,0.15);color:var(--color-primary-700);padding:6px 12px;border-radius:var(--radius-sm);font-size:13px;font-weight:600">✨
              Sistem Penerimaan Siswa Baru</span>
            <h1 style="margin-top:14px;line-height:1.2;color:var(--color-white)">Daftar dan Raih<br><span
                style="color:var(--color-secondary)">Masa Depan Cemerlang</span></h1>
            <p style="color:rgba(255,255,255,.9); margin-bottom: 24px;">Bergabunglah dengan ribuan calon siswa yang
              telah mendaftar di SPMB {{ $schoolIdentity->school_name ?? 'Tunas Luhur' }}. Proses pendaftaran mudah, transparan, dan dapat dilacak secara
              real-time melalui dashboard Anda.</p>
            <div style="margin-top:16px">
              @auth
                @if(auth()->user()->hasRole('Applicant'))
                  <a href="{{ route('portal.dashboard') }}" class="btn-base btn-secondary">Buka Dashboard Portal</a>
                @else
                  <a href="{{ route('admin.dashboard') }}" class="btn-base btn-secondary">Buka Dashboard Admin</a>
                @endif
              @else
                <a href="{{ route('applicant.register') }}" class="btn-base btn-secondary">Mulai Daftar</a>
                <a href="{{ route('applicant.login') }}" class="btn-base btn-hero-outline" style="margin-left:10px">Sudah Punya
                  Akun?</a>
              @endauth
            </div>
          </div>
          <div style="width:320px;min-width:260px;text-align:center" data-aos="fade-left" data-aos-duration="1000">
            <div style="display:inline-block;transform:translateY(0);animation:float 3s ease-in-out infinite;">
              <img src="{{ $schoolIdentity && $schoolIdentity->logo ? asset('storage/' . $schoolIdentity->logo) : asset('template/landing/assets/img/logo/logo-sma.png') }}" alt="logo" style="width:220px;box-shadow:var(--shadow-lg)">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
      <div class="container-xxl">
        <div class="stats-grid">
          <div class="stat-card accent" data-aos="zoom-in" data-aos-delay="100" data-aos-duration="700">
            <div style="font-size:24px;color:var(--color-accent);margin-bottom:8px"><i class="fas fa-users"></i></div>
            <h3>{{ $activeConfig ? number_format(max(0, $tracks->sum('enrollments_count')), 0, ',', '.') : '0' }}</h3>
            <div class="text-muted">Pendaftar Terdaftar</div>
          </div>
          <div class="stat-card success" data-aos="zoom-in" data-aos-delay="200" data-aos-duration="700">
            <div style="font-size:24px;color:var(--color-success);margin-bottom:8px"><i class="fas fa-check-circle"></i>
            </div>
            <h3>{{ $activeConfig ? number_format(max(0, $tracks->sum('quota')), 0, ',', '.') : '0' }}</h3>
            <div class="text-muted">Total Kuota Tersedia</div>
          </div>
          <div class="stat-card primary" data-aos="zoom-in" data-aos-delay="300" data-aos-duration="700">
            <div style="font-size:24px;color:var(--color-primary-600);margin-bottom:8px"><i
                class="fas fa-graduation-cap"></i></div>
            <h3>{{ count($tracks) }}</h3>
            <div class="text-muted">Jalur Pendaftaran Aktif</div>
          </div>
          <div class="stat-card warning" data-aos="zoom-in" data-aos-delay="400" data-aos-duration="700">
            <div style="font-size:24px;color:var(--color-warning);margin-bottom:8px"><i class="fas fa-percentage"></i>
            </div>
            <h3>{{ $activeConfig->academicYear->year ?? 'Aktif' }}</h3>
            <div class="text-muted">Tahun Ajaran SPMB</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Jalur Pendaftaran Section -->
    <section id="jalur-pendaftaran" style="padding:48px 0;background:var(--color-bg)">
      <div class="container-xxl">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px"
          data-aos="fade-down" data-aos-duration="800">
          <div>
            <h2 style="margin:0">Jalur <span>Pendaftaran</span></h2>
            <p style="color:var(--color-text-muted);margin:6px 0 0">Pilih jalur pendaftaran sesuai kualifikasi dan minat
              Anda</p>
          </div>
          @auth
            @if(auth()->user()->hasRole('Applicant'))
              <a href="{{ route('portal.dashboard') }}" style="color:var(--color-accent);text-decoration:none;font-weight:600">Buka Dashboard Portal <i class="fas fa-arrow-right ms-2"></i></a>
            @else
              <a href="{{ route('admin.dashboard') }}" style="color:var(--color-accent);text-decoration:none;font-weight:600">Buka Dashboard Admin <i class="fas fa-arrow-right ms-2"></i></a>
            @endif
          @else
            <a href="{{ route('applicant.register') }}" style="color:var(--color-accent);text-decoration:none;font-weight:600">Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i></a>
          @endauth
        </div>

        <div class="tracks-grid">
          @forelse ($tracks as $index => $track)
            @php
              // Calculate remaining quota
              $sisaKuota = max(0, $track->quota - $track->enrollments_count);
              
              // Choose color/emoji/gradient based on track code or index
              $emojis = ['🎓', '🏅', '⭐', '📋', '⚡', '✨'];
              $emoji = $emojis[$index % count($emojis)];
              
              $gradients = [
                'linear-gradient(135deg, rgba(11,74,111,0.1), rgba(242,201,76,0.1))',
                'linear-gradient(135deg, rgba(0,119,204,0.1), rgba(242,201,76,0.1))',
                'linear-gradient(135deg, rgba(40,167,69,0.1), rgba(242,201,76,0.1))',
                'linear-gradient(135deg, rgba(242,153,74,0.1), rgba(11,74,111,0.1))'
              ];
              $gradient = $gradients[$index % count($gradients)];
              
              $targetUrl = auth()->check() ? (auth()->user()->hasRole('Applicant') ? route('portal.dashboard') : route('admin.dashboard')) : route('applicant.login');
            @endphp
            <div data-aos="flip-left" data-aos-delay="{{ ($index + 1) * 100 }}" data-aos-duration="900" style="display: flex;">
              <a href="{{ $sisaKuota > 0 ? $targetUrl : 'javascript:void(0)' }}" class="track-card w-100 {{ $sisaKuota <= 0 ? 'disabled' : '' }}" style="text-decoration: none;">
                <div class="track-cover" style="background: {{ $gradient }};">{{ $emoji }}</div>
                <div style="font-weight:700;color:var(--color-primary-600);font-size:18px;margin-top:12px">{{ $track->trackType->name }}</div>
                <div style="color:var(--color-text-muted);font-size:13px;margin-top:6px;min-height:38px;line-height:1.4">{{ $track->trackType->description ?? 'Penerimaan melalui jalur ' . $track->trackType->name }}</div>
                
                <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                  <div>
                    <small class="text-muted d-block" style="font-size: 11px;">Biaya Pendaftaran</small>
                    <span class="fw-bold text-primary-900" style="font-size: 14px;">
                      @if ($track->registration_fee > 0)
                        Rp {{ number_format($track->registration_fee, 0, ',', '.') }}
                      @else
                        Gratis
                      @endif
                    </span>
                  </div>
                  <div class="text-end">
                    <small class="text-muted d-block" style="font-size: 11px;">Sisa Kuota</small>
                    @if ($sisaKuota > 0)
                      <span class="badge bg-success text-white px-2 py-1" style="font-size: 11px;">{{ $sisaKuota }} / {{ $track->quota }} Kursi</span>
                    @else
                      <span class="badge bg-danger text-white px-2 py-1" style="font-size: 11px;">Penuh</span>
                    @endif
                  </div>
                </div>
              </a>
            </div>
          @empty
            <div class="w-100 text-center py-5" data-aos="fade-up">
              <div class="fs-1 text-muted" style="font-size: 48px !important;"><i class="fa-solid fa-calendar-xmark"></i></div>
              <h5 class="mt-3 text-muted">Belum Ada Jalur Pendaftaran yang Dibuka</h5>
              <p class="text-muted mb-0">Silakan pantau berkala halaman ini untuk pembaruan jadwal SPMB.</p>
            </div>
          @endforelse
        </div>
      </div>
    </section>

    <!-- Alur Pendaftaran Section -->
    <section id="alur-pendaftaran" style="padding:48px 0;background:var(--color-surface);overflow-x:hidden">
      <div class="container-xxl">
        <div style="margin-bottom:28px" data-aos="fade-right" data-aos-duration="800">
          <h2>📋 Alur <span>Pendaftaran</span></h2>
          <p style="color:var(--color-text-muted);margin:6px 0 0">Langkah-langkah mudah dari awal pendaftaran hingga resmi diterima</p>
        </div>

        <div class="steps-grid">
          <div class="step-card" data-aos="slide-right" data-aos-delay="100" data-aos-duration="700">
            <div class="step-icon"><i class="fas fa-user-plus"></i></div>
            <div class="step-number">01</div>
            <h4>Buat Akun</h4>
            <p>Registrasi akun calon siswa mandiri di portal dengan email & nomor telepon aktif.</p>
          </div>
          <div class="step-card" data-aos="zoom-in-up" data-aos-delay="150" data-aos-duration="700">
            <div class="step-icon"><i class="fas fa-edit"></i></div>
            <div class="step-number">02</div>
            <h4>Isi Formulir</h4>
            <p>Lengkapi data profil diri dan data akademik sesuai persyaratan jalur yang dipilih.</p>
          </div>
          <div class="step-card" data-aos="slide-left" data-aos-delay="200" data-aos-duration="700">
            <div class="step-icon"><i class="fas fa-wallet"></i></div>
            <div class="step-number">03</div>
            <h4>Pembayaran</h4>
            <p>Bayar biaya pendaftaran (bila ada) dan unggah bukti bayar untuk diverifikasi admin.</p>
          </div>
          <div class="step-card" data-aos="slide-right" data-aos-delay="250" data-aos-duration="700">
            <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
            <div class="step-number">04</div>
            <h4>Verifikasi & Seleksi</h4>
            <p>Admin memverifikasi berkas Anda, diikuti dengan pelaksanaan tes sesuai jadwal.</p>
          </div>
          <div class="step-card" data-aos="zoom-in-up" data-aos-delay="300" data-aos-duration="700">
            <div class="step-icon"><i class="fas fa-bullhorn"></i></div>
            <div class="step-number">05</div>
            <h4>Pengumuman</h4>
            <p>Lihat status kelulusan akhir secara langsung di dashboard akun pendaftaran Anda.</p>
          </div>
          <div class="step-card" data-aos="slide-left" data-aos-delay="350" data-aos-duration="700">
            <div class="step-icon"><i class="fas fa-file-signature"></i></div>
            <div class="step-number">06</div>
            <h4>Daftar Ulang</h4>
            <p>Lakukan pembayaran daftar ulang, finalisasi, dan unduh Surat Pernyataan Siswa Tetap.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Persyaratan Dokumen Section -->
    <section id="persyaratan" style="padding:48px 0;background:var(--color-bg)">
      <div class="container-xxl">
        <div style="margin-bottom:28px" data-aos="fade-left" data-aos-duration="800">
          <h2>📂 Persyaratan <span>Dokumen</span></h2>
          <p style="color:var(--color-text-muted);margin:6px 0 0">Dokumen-dokumen wajib yang harus dipersiapkan sebelum mendaftar</p>
        </div>

        <div class="requirements-grid">
          <div class="requirement-card" data-aos="zoom-in-right" data-aos-delay="100" data-aos-duration="800">
            <div class="requirement-icon"><i class="fas fa-id-card"></i></div>
            <h4>Dokumen Identitas</h4>
            <ul>
              <li>Kartu Keluarga (KK) asli (scan/foto)</li>
              <li>Akta Kelahiran calon siswa (scan/foto)</li>
              <li>KTP Orang Tua/Wali (scan/foto)</li>
            </ul>
          </div>
          <div class="requirement-card" data-aos="zoom-in-up" data-aos-delay="200" data-aos-duration="800">
            <div class="requirement-icon"><i class="fas fa-graduation-cap"></i></div>
            <h4>Dokumen Akademik</h4>
            <ul>
              <li>Nomor Induk Siswa Nasional (NISN) valid</li>
              <li>Rapor SMP/Sederajat 5 semester terakhir</li>
              <li>Surat Keterangan Lulus (SKL) / Ijazah</li>
            </ul>
          </div>
          <div class="requirement-card" data-aos="zoom-in-left" data-aos-delay="300" data-aos-duration="800">
            <div class="requirement-icon"><i class="fas fa-file-medical"></i></div>
            <h4>Dokumen Pendukung</h4>
            <ul>
              <li>Pasfoto berwarna terbaru calon siswa (JPG/PNG)</li>
              <li>Sertifikat Prestasi/Piagam Tahfidz (jika ada)</li>
              <li>Bukti Pembayaran Pendaftaran</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" style="padding:48px 0;background:var(--color-surface)">
      <div class="container-xxl">
        <div style="margin-bottom:28px;text-align:center" data-aos="zoom-in-down" data-aos-duration="800">
          <h2>❓ Pertanyaan <span>Sering Diajukan (FAQ)</span></h2>
          <p style="color:var(--color-text-muted);margin:6px 0 0">Temukan jawaban atas pertanyaan-pertanyaan umum seputar proses SPMB Tunas Luhur</p>
        </div>

        <div class="faq-container" data-aos="fade-left" data-aos-delay="150" data-aos-duration="850">
          <div class="accordion" id="accordionFAQ">
            <div class="accordion-item faq-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                  Jika saya tidak lulus seleksi di salah satu jalur, apakah bisa mendaftar di jalur lain?
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                data-bs-parent="#accordionFAQ">
                <div class="accordion-body faq-answer">
                  Ya, Anda bisa. Calon siswa yang berstatus <strong>Tidak Lulus (REJECTED)</strong> pada suatu jalur
                  dapat langsung mendaftar kembali ke jalur lain yang masih aktif tanpa perlu membuat akun baru. Anda
                  cukup memilih jalur baru melalui dashboard Anda.
                </div>
              </div>
            </div>
            <div class="accordion-item faq-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Apakah biaya pendaftaran awal yang sudah dibayar bisa dialihkan jika saya mendaftar ke jalur lain?
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                data-bs-parent="#accordionFAQ">
                <div class="accordion-body faq-answer">
                  Jika admin sekolah mengaktifkan opsi <strong>carryover</strong> pada sistem, biaya pendaftaran yang
                  sudah dibayar pada jalur sebelumnya yang gagal dapat digunakan sebagai deposit pemotong biaya
                  pendaftaran di jalur baru, sehingga Anda tidak perlu membayar penuh lagi.
                </div>
              </div>
            </div>
            <div class="accordion-item faq-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Bagaimana mekanisme pembayaran biaya daftar ulang?
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                data-bs-parent="#accordionFAQ">
                <div class="accordion-body faq-answer">
                  Bagi calon siswa yang dinyatakan lulus (PASSED), sistem mendukung pembayaran daftar ulang secara
                  <strong>cicilan</strong>. Anda dapat mengunggah bukti cicilan bertahap. Sistem akan menampilkan
                  progress pelunasan secara real-time, dan tombol finalisasi akan aktif setelah total cicilan lunas.
                </div>
              </div>
            </div>
            <div class="accordion-item faq-item">
              <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed faq-button" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                  Kapan saya bisa mengunduh Surat Pernyataan Siswa Tetap?
                </button>
              </h2>
              <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                data-bs-parent="#accordionFAQ">
                <div class="accordion-body faq-answer">
                  Surat Pernyataan Siswa Tetap dalam bentuk PDF resmi dapat diunduh langsung dari dashboard pendaftaran
                  Anda segera setelah seluruh tagihan daftar ulang berstatus lunas (SETTLED) dan Anda telah melakukan
                  konfirmasi finalisasi daftar ulang di sistem.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action Section -->
    <section style="padding:18px 0;background:var(--color-white)" data-aos="fade-up" data-aos-duration="1000"
      data-aos-offset="80">
      <div class="container-xxl">
        <div class="cta">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
            <div>
              <div style="font-size:14px;opacity:.9; font-weight: 600">⏰ Pendaftaran Dibuka</div>
              <h3 style="margin:6px 0">Jangan Lewatkan Kesempatan Ini!</h3>
              <div style="max-width:560px;color:rgba(255,255,255,.95)">
                Pendaftaran SPMB {{ $schoolIdentity->school_name ?? 'Tunas Luhur' }} masih dibuka untuk tahun pelajaran {{ $activeConfig->academicYear->year ?? '' }}. 
                Proses pendaftaran dan verifikasi terpusat, transparan, dan dapat dipantau dari gawai Anda.
              </div>
            </div>
            <div style="text-align:right">
              @auth
                @if(auth()->user()->hasRole('Applicant'))
                  <a href="{{ route('portal.dashboard') }}" class="btn-base btn-secondary">Dashboard Portal</a>
                @else
                  <a href="{{ route('admin.dashboard') }}" class="btn-base btn-secondary">Dashboard Admin</a>
                @endif
              @else
                <a href="{{ route('applicant.register') }}" class="btn-base btn-secondary">Daftar Sekarang</a>
              @endauth
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section style="padding:18px 0;background:var(--color-white)" data-aos="zoom-in-up" data-aos-duration="900"
      data-aos-offset="60">
      <div class="container-xxl">
        <div class="newsletter">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div style="min-width:220px; flex: 1">
              <h3 style="margin:0">📧 Dapatkan Update Terbaru</h3>
              <div style="opacity:.85; font-size:14px; margin-top:4px">Langgani newsletter untuk mendapatkan info jadwal
                tes, pengumuman hasil, dan tips persiapan ujian langsung ke email Anda.</div>
            </div>
            <form style="display:flex;gap:8px;max-width:480px;width:100%;flex-shrink:0">
              <input type="email" placeholder="Masukkan email Anda"
                style="flex:1;padding:12px;border-radius:var(--radius-sm);border:0" required>
              <button type="submit" class="btn-base btn-secondary" style="border:0">Subscribe</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="container-xxl">
      <div>© <strong>SPMB {{ $schoolIdentity->school_name ?? 'Tunas Luhur' }}</strong> — Sistem Penerimaan Siswa Baru. Semua hak dilindungi.</div>
    </div>
  </footer>

  <!-- Vendor Scripts -->
  <script src="{{ asset('template/landing/assets/js/vendor/bootstrap.bundle.min.js') }}"></script>

  <!-- AOS Script & Init -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 800,
      once: true,
      easing: 'ease-in-out'
    });
  </script>

  <!-- Responsive Header Script -->
  <script>
    (function () {
      const toggle = document.getElementById('navToggle');
      const mobileNav = document.getElementById('navMobile');
      const overlay = document.getElementById('navOverlay');
      const header = document.getElementById('main-header');

      if (!toggle || !mobileNav || !overlay) return;

      /* ---- Open / Close helpers ---- */
      function openNav() {
        toggle.classList.add('active');
        mobileNav.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
        mobileNav.setAttribute('aria-hidden', 'false');
        overlay.style.display = 'block';
        requestAnimationFrame(() => overlay.style.opacity = '1');
        document.body.style.overflow = 'hidden';
      }

      function closeNav() {
        toggle.classList.remove('active');
        mobileNav.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
        mobileNav.setAttribute('aria-hidden', 'true');
        overlay.style.opacity = '0';
        setTimeout(() => { overlay.style.display = 'none'; }, 300);
        document.body.style.overflow = '';
      }

      /* ---- Toggle on hamburger click ---- */
      toggle.addEventListener('click', () => {
        mobileNav.classList.contains('open') ? closeNav() : openNav();
      });

      /* ---- Close on overlay click ---- */
      overlay.addEventListener('click', closeNav);

      /* ---- Close when a mobile link is tapped ---- */
      mobileNav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', closeNav);
      });

      /* ---- Close & reset on resize to desktop ---- */
      window.addEventListener('resize', () => {
        if (window.innerWidth > 768) closeNav();
      });

      /* ---- Sticky header shadow on scroll ---- */
      window.addEventListener('scroll', () => {
        header.classList.toggle('scrolled', window.scrollY > 10);
      }, { passive: true });

      /* ---- Keyboard: close on Escape ---- */
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileNav.classList.contains('open')) closeNav();
      });
    })();
  </script>
</body>

</html>
