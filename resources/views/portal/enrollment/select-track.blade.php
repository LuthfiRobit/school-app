@extends('portal.layouts.app')

@section('title', 'Pilih Jalur Pendaftaran')

@section('content')
<div x-data="pilihJalurApp()" class="w-100">

  <!-- Page Header & Breadcrumb -->
  <div class="mb-4" data-aos="fade-up" data-aos-duration="600">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2 small">
        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}" class="text-primary-600 text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pilih Jalur</li>
      </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
      <div>
        <h4 class="fw-bold text-primary-900 mb-1">Pilih Jalur Pendaftaran</h4>
        <p class="text-muted mb-0">Silakan pilih jalur pendaftaran aktif yang sesuai kualifikasi dan persyaratan akademik Anda.</p>
      </div>
      <div class="mt-3 mt-md-0">
        <a href="{{ route('portal.dashboard') }}" class="btn btn-sm btn-outline-secondary px-3 py-2 border-primary text-primary-600">
          <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
      </div>
    </div>
  </div>

  <!-- FLOATING PROTOTYPE STATE SIMULATOR (LOCAL ENVIRONMENT ONLY) -->
  @if (config('app.env') === 'local')
    <div class="portal-simulator-widget shadow-lg" x-data="{ collapsed: true }" :class="{ 'collapsed': collapsed }" style="z-index: 1050;">
      <div class="widget-header d-flex align-items-center justify-content-between border-bottom pb-2 mb-2 bg-primary text-white rounded-top p-2" style="margin: -10px -10px 10px -10px;">
        <span class="fw-bold small"><i class="fa-solid fa-vial me-1"></i> SIMULATOR STATUS</span>
        <button class="btn btn-sm text-white p-0" @click="collapsed = !collapsed">
          <i class="fa-solid" :class="collapsed ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        </button>
      </div>

      <div class="widget-content" x-show="!collapsed" style="display: none;">
        <p class="small text-muted mb-2">Simulasikan keadaan pendaftar saat memilih jalur:</p>
        <div class="d-grid gap-1">
          <button class="btn btn-sm btn-outline-primary text-start" :class="{ 'active bg-primary text-white': simulationState === 'none' }" @click="simulationState = 'none'">1. Normal (Akun Baru)</button>
          <button class="btn btn-sm btn-outline-warning text-start" :class="{ 'active bg-warning text-dark': simulationState === 'active_enrolled' }" @click="simulationState = 'active_enrolled'">2. Sudah Mendaftar Jalur Lain</button>
          <button class="btn btn-sm btn-outline-info text-start" :class="{ 'active bg-info text-white': simulationState === 're_apply' }" @click="simulationState = 're_apply'">3. Gagal Seleksi (Re-Apply)</button>
          <button class="btn btn-sm btn-outline-danger text-start" :class="{ 'active bg-danger text-white': simulationState === 'quota_full' }" @click="simulationState = 'quota_full'">4. Kuota Semua Jalur Penuh</button>
        </div>
      </div>
    </div>
  @endif

  <!-- ALERT NOTIFIKASI SIMULATOR AKTIF -->
  <div class="alert alert-warning border border-warning border-opacity-50 p-3 mb-4 d-flex align-items-center justify-content-between"
    x-show="simulationState !== 'none'" x-transition style="display: none;">
    <div class="d-flex align-items-center">
      <i class="fa-solid fa-triangle-exclamation text-warning fs-3 me-3"></i>
      <div>
        <strong class="text-primary-900 block d-block mb-1">Mode Simulasi Aktif:</strong>
        <span class="text-muted small" x-show="simulationState === 'active_enrolled'">Mensimulasikan pendaftar yang sudah memiliki pendaftaran berjalan pada jalur lain.</span>
        <span class="text-muted small" x-show="simulationState === 're_apply'">Mensimulasikan pendaftar yang dinyatakan gagal seleksi dan diizinkan mendaftar jalur lain (Re-Apply).</span>
        <span class="text-muted small" x-show="simulationState === 'quota_full'">Mensimulasikan semua sisa kuota pendaftaran habis.</span>
      </div>
    </div>
    <button class="btn btn-sm btn-dark text-white px-3 py-1" @click="simulationState = 'none'">Reset</button>
  </div>

  <!-- Filter & Search Controls -->
  <div class="card p-3 mb-4 shadow-sm border-0" data-aos="fade-up" data-aos-duration="600" data-aos-delay="50">
    <div class="row g-3 align-items-center">
      <!-- Search bar -->
      <div class="col-md-5">
        <div class="position-relative">
          <i class="fa-solid fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
          <input type="text" class="form-control ps-5" placeholder="Cari nama jalur atau persyaratan..." x-model="searchQuery">
        </div>
      </div>
      <!-- Category Filters -->
      <div class="col-md-7 text-md-end">
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
          <button class="category-filter-btn btn" :class="selectedCategory === 'all' && 'active'" @click="selectedCategory = 'all'">Semua Jalur</button>
          <button class="category-filter-btn btn" :class="selectedCategory === 'no_test' && 'active'" @click="selectedCategory = 'no_test'">Tanpa Ujian</button>
          <button class="category-filter-btn btn" :class="selectedCategory === 'test' && 'active'" @click="selectedCategory = 'test'">Dengan Ujian CBT</button>
          <button class="category-filter-btn btn" :class="selectedCategory === 'boarding' && 'active'" @click="selectedCategory = 'boarding'">Asrama / Boarding</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Tracks Grid -->
  <div class="row g-4 mb-4">
    <template x-for="track in filteredTracks" :key="track.id">
      @include('portal.enrollment.partials.track-card')
    </template>

    <!-- Empty State if no tracks match filters -->
    <div class="col-12 text-center py-5" x-show="filteredTracks.length === 0" x-transition style="display: none;">
      <div class="text-muted mb-2"><i class="fa-regular fa-compass fs-1"></i></div>
      <h6 class="fw-bold text-primary-900">Jalur tidak ditemukan</h6>
      <p class="text-muted small mb-0">Silakan ubah filter pencarian Anda atau periksa kembali ejaan.</p>
    </div>
  </div>

  <!-- CONFIRMATION TERMS MODAL (Alpine.js glassmorphism modal) -->
  <div class="glass-modal-backdrop" x-show="showModal" style="display: none;" x-transition>
    <div class="glass-modal-content p-4" @click.outside="showModal = false">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary-900 mb-0"><i class="fa-solid fa-file-signature text-primary-600 me-2"></i> Konfirmasi Pemilihan</h5>
        <button class="btn-close btn btn-sm" @click="showModal = false"></button>
      </div>
      
      <template x-if="selectedTrack">
        <div class="modal-body p-0 mb-4">
          <p class="text-muted small">Anda telah memilih untuk mendaftar pada jalur berikut:</p>
          
          <div class="p-3 bg-light rounded-md border mb-3">
            <div class="d-flex align-items-center mb-2">
              <span class="fs-4 me-2" x-text="selectedTrack.icon"></span>
              <strong class="text-primary-900" x-text="selectedTrack.name"></strong>
            </div>
            <span class="text-muted small d-block mb-1">Rincian Biaya Formulir:</span>
            <strong class="text-primary-950 fs-5" x-text="formatCurrency(selectedTrack.fee)"></strong>
          </div>

          <div class="alert alert-warning p-2 small border border-warning border-opacity-25 mb-3">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Penting:</strong> Setelah Anda menekan tombol "Lanjutkan", pilihan jalur Anda akan dikunci sementara untuk mengisi formulir pendaftaran. Anda tidak dapat memilih jalur lain hingga pendaftaran ini dibatalkan atau diselesaikan.
          </div>

          <!-- Terms and Conditions checkbox -->
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="termsCheck" x-model="agreedToTerms">
            <label class="form-check-label text-muted small cursor-pointer" for="termsCheck">
              Saya menyetujui persyaratan pendaftaran pada <span class="fw-bold" x-text="selectedTrack.name"></span> serta bersedia mengunggah dokumen yang valid dan sah.
            </label>
          </div>
        </div>
      </template>

      <div class="d-flex gap-2 justify-content-end">
        <button class="btn btn-sm btn-outline-secondary px-3 py-2 border-primary text-primary-600" @click="showModal = false">Batal</button>
        <button class="btn btn-sm btn-primary px-4 py-2" 
          :disabled="!agreedToTerms" 
          @click="confirmSelection()">Lanjutkan Pendaftaran <i class="fa-solid fa-chevron-right ms-1 fs-xs"></i></button>
      </div>
    </div>
  </div>

</div>
@endsection

@push('js')
<script>
  function pilihJalurApp() {
    return {
      mobileMenuOpen: false,
      searchQuery: '',
      selectedCategory: 'all',
      simulationState: 'none',
      showModal: false,
      selectedTrack: null,
      agreedToTerms: false,

      // Generated dynamically from DB
      tracks: {!! json_encode($tracks->map(function($track) {
          // Determine used quota
          $quotaUsed = $track->enrollments_count;
          
          // Determine category
          $category = 'test';
          $nameLower = strtolower($track->trackType->name);
          if (str_contains($nameLower, 'prestasi') || str_contains($nameLower, 'umum') || str_contains($nameLower, 'berkas')) {
              $category = 'no_test';
          }
          if (str_contains($nameLower, 'asrama') || str_contains($nameLower, 'boarding') || str_contains($nameLower, 'tahfidz') || str_contains($nameLower, 'unggulan')) {
              $category = 'boarding';
          }

          $emojis = ['🎓', '✍️', '⭐', '📋', '⚡', '✨'];
          $icon = $emojis[$track->id % count($emojis)];

          $iconColors = ['#0b4a6f', '#0077cc', '#28a745', '#f2994a', '#a855f7', '#ec4899'];
          $iconColor = $iconColors[$track->id % count($iconColors)];

          $gradients = [
            'linear-gradient(135deg, rgba(11,74,111,0.1), rgba(242,201,76,0.1))',
            'linear-gradient(135deg, rgba(0,119,204,0.1), rgba(242,201,76,0.1))',
            'linear-gradient(135deg, rgba(40,167,69,0.1), rgba(242,201,76,0.1))',
            'linear-gradient(135deg, rgba(242,153,74,0.1), rgba(11,74,111,0.1))'
          ];
          $bgGradient = $gradients[$track->id % count($gradients)];

          // Requirements
          $requirements = [];
          foreach ($track->assessments as $assess) {
              $requirements[] = 'Mengikuti seleksi ' . $assess->assessmentType->name;
          }
          $requirements[] = 'Upload Pasfoto, KK & Akta Kelahiran';
          $requirements[] = 'Nomor Induk Siswa Nasional (NISN) aktif';

          // Tags
          $tags = [];
          if ($category === 'no_test') { $tags[] = 'Tanpa Tes'; }
          else if ($category === 'test') { $tags[] = 'CBT / Ujian'; }
          else { $tags[] = 'Boarding'; }
          $tags[] = $track->trackType->name;

          return [
              'id' => $track->id,
              'name' => $track->trackType->name,
              'category' => $category,
              'icon' => $icon,
              'iconColor' => $iconColor,
              'bgGradient' => $bgGradient,
              'quotaMax' => $track->quota,
              'quotaUsed' => $quotaUsed,
              'fee' => (float)$track->registration_fee,
              'description' => $track->trackType->description ?? 'Penerimaan calon siswa baru melalui jalur ' . $track->trackType->name . '.',
              'requirements' => $requirements,
              'tags' => $tags
          ];
      })) !!},

      get filteredTracks() {
        return this.tracks.filter(track => {
          const matchesSearch = track.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                track.description.toLowerCase().includes(this.searchQuery.toLowerCase());
          const matchesCategory = this.selectedCategory === 'all' || track.category === this.selectedCategory;
          return matchesSearch && matchesCategory;
        });
      },

      getQuotaLeft(track) {
        if (this.simulationState === 'quota_full') return 0;
        return Math.max(0, track.quotaMax - track.quotaUsed);
      },

      isQuotaFull(track) {
        return this.getQuotaLeft(track) <= 0;
      },

      isTrackDisabled(track) {
        if (this.simulationState === 'active_enrolled') return true;
        if (this.simulationState === 're_apply' && track.id === 1) return true; // Pretend first track failed
        return this.isQuotaFull(track);
      },

      getDisabledReason(track) {
        if (this.simulationState === 'active_enrolled') {
          return 'Anda sudah memiliki pendaftaran berjalan di jalur pendaftaran lain.';
        }
        if (this.simulationState === 're_apply' && track.id === 1) {
          return 'Anda tidak diperbolehkan mendaftar kembali pada jalur yang pernah gagal.';
        }
        if (this.isQuotaFull(track)) {
          return 'Kuota pendaftaran untuk jalur ini sudah terpenuhi.';
        }
        return '';
      },

      getReApplyRecommendation(track) {
        return this.simulationState === 're_apply' && track.id === 2; // Suggest CBT if Prestasi fails
      },

      selectTrack(track) {
        if (this.isTrackDisabled(track)) return;
        this.selectedTrack = track;
        this.agreedToTerms = false;
        this.showModal = true;
      },

      confirmSelection() {
        if (!this.agreedToTerms) return;
        this.showModal = false;
        
        // Redirect dynamically to Laravel route
        const url = '{{ route("portal.enrollment.form", ":id") }}'.replace(':id', this.selectedTrack.id);
        window.location.href = url;
      },

      formatCurrency(amount) {
        if (amount <= 0) return 'Gratis';
        return 'Rp ' + amount.toLocaleString('id-ID');
      }
    }
  }
</script>
@endpush
