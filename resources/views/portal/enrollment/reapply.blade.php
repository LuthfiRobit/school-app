@extends('portal.layouts.app')

@section('title', 'Pilih Jalur Ulang (Re-Apply)')

@section('content')
<div x-data="reApplyApp()" class="w-100">

  <!-- Page Header & Breadcrumb -->
  <div class="mb-4" data-aos="fade-up" data-aos-duration="600">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2 small">
        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}" class="text-primary-600 text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pilih Jalur Ulang (Re-Apply)</li>
      </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
      <div>
        <h4 class="fw-bold text-primary-900 mb-1">Pendaftaran Ulang — Pilih Jalur Alternatif</h4>
        <p class="text-muted mb-0">Anda masih memiliki kesempatan mendaftar melalui jalur lain yang tersedia.</p>
      </div>
      <div class="mt-3 mt-md-0">
        <a href="{{ route('portal.dashboard') }}" class="btn btn-sm btn-outline-secondary px-3 py-2 border-primary text-primary-600">
          <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
      </div>
    </div>
  </div>

  <!-- REJECTION SUMMARY CALLOUT -->
  <div class="card shadow-sm border-0 border-start-error mb-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="50">
    <div class="card-body p-4">
      <div class="d-flex align-items-start">
        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 44px; height: 44px;">
          <i class="fa-solid fa-circle-xmark fs-5"></i>
        </div>
        <div class="flex-grow-1">
          <h5 class="fw-bold text-primary-900 mb-2">Hasil Seleksi Sebelumnya: <span class="text-error">TIDAK LULUS</span></h5>
          <div class="row g-3 mb-3">
            <div class="col-sm-4">
              <span class="text-muted small d-block">Jalur Ditolak:</span>
              <strong class="text-primary-900">{{ $lastEnrollment->spmbTrack->trackType->name }}</strong>
            </div>
            <div class="col-sm-4">
              <span class="text-muted small d-block">Tanggal Keputusan:</span>
              <strong class="text-primary-900">{{ $rejectedLog ? $rejectedLog->created_at->translatedFormat('d F Y') : $lastEnrollment->updated_at->translatedFormat('d F Y') }}</strong>
            </div>
            <div class="col-sm-4">
              <span class="text-muted small d-block">Alasan Penolakan:</span>
              <strong class="text-error">{{ $rejectedLog && $rejectedLog->reason ? $rejectedLog->reason : 'Skor kelulusan belum memenuhi standar minimum jalur pendaftaran yang dipilih.' }}</strong>
            </div>
          </div>
          <div class="alert alert-info d-flex align-items-start p-2 mb-0 border border-info border-opacity-25">
            <i class="fa-solid fa-lightbulb text-primary-600 me-2 mt-1"></i>
            <div class="small text-muted">
              <strong class="text-primary-800">Jangan berkecil hati!</strong> Anda masih memiliki kesempatan yang sama untuk diterima melalui jalur pendaftaran alternatif di bawah ini. Data profil dasar Anda (nama, NISN, sekolah asal) akan otomatis terbawa ke formulir pendaftaran jalur baru.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter & Search Controls -->
  <div class="card p-3 mb-4 shadow-sm border-0" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
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
          <button class="category-filter-btn btn" :class="selectedCategory === 'test' && 'active'" @click="selectedCategory = 'test'">Dengan Ujian</button>
          <button class="category-filter-btn btn" :class="selectedCategory === 'boarding' && 'active'" @click="selectedCategory = 'boarding'">Asrama & Boarding</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Tracks Grid -->
  <div class="row g-4 mb-4">
    <template x-for="track in filteredTracks" :key="track.id">
      <div class="col-md-6" data-aos="fade-up" data-aos-duration="600" data-aos-delay="150">
        <div class="card track-selection-card shadow-sm border-0 d-flex flex-column h-100 position-relative"
          :class="{
            'rejected-track disabled': isRejectedTrack(track),
            'recommended-track pulse-recommend': isRecommended(track) && !isRejectedTrack(track),
            'disabled': isTrackDisabled(track) && !isRejectedTrack(track)
          }">
          
          <!-- Rejected Overlay Badge -->
          <template x-if="isRejectedTrack(track)">
            <div class="rejected-overlay-badge">
              <span class="badge bg-danger text-white px-3 py-2 shadow-sm">
                <i class="fa-solid fa-ban me-1"></i> DITOLAK
              </span>
            </div>
          </template>

          <!-- Recommended Overlay Badge -->
          <template x-if="isRecommended(track) && !isRejectedTrack(track)">
            <div class="rejected-overlay-badge">
              <span class="badge bg-success text-white px-3 py-2 shadow-sm">
                <i class="fa-solid fa-star me-1 text-warning"></i> Direkomendasikan
              </span>
            </div>
          </template>

          <div class="card-body p-4 d-flex flex-column justify-content-between h-100">
            <div>
              <!-- Top Badge & Header -->
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="track-icon-wrapper" :style="'background: ' + track.bgGradient + '; color: ' + track.iconColor">
                  <span x-text="track.icon"></span>
                </div>
                
                <div class="d-flex flex-column align-items-end">
                  <!-- Quota Badges -->
                  <template x-if="isRejectedTrack(track)">
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 small">
                      <i class="fa-solid fa-ban me-1"></i> Jalur Ditolak
                    </span>
                  </template>
                  <template x-if="!isRejectedTrack(track) && isQuotaFull(track)">
                    <span class="badge bg-danger text-white">Kuota Penuh</span>
                  </template>
                  <template x-if="!isRejectedTrack(track) && !isQuotaFull(track)">
                    <span class="badge bg-light text-primary-800 border border-primary border-opacity-25" 
                      x-text="'Sisa ' + getQuotaLeft(track) + ' Kuota'"></span>
                  </template>
                </div>
              </div>

              <!-- Title & Tags -->
              <h5 class="fw-bold text-primary-900 mb-1" x-text="track.name"></h5>
              <div class="d-flex flex-wrap gap-1 mb-3">
                <template x-for="tag in track.tags">
                  <span class="tag-pill bg-light text-muted" x-text="tag"></span>
                </template>
              </div>

              <!-- Description -->
              <p class="text-muted small leading-relaxed mb-4" x-text="track.description"></p>

              <!-- Requirements Checklist -->
              <div class="p-3 bg-light rounded-md mb-4 border">
                <h6 class="fw-bold text-primary-800 fs-xs text-uppercase tracking-wider mb-2"><i class="fa-solid fa-clipboard-list me-1"></i> Syarat Utama:</h6>
                <ul class="requirements-check-list p-0 m-0">
                  <template x-for="req in track.requirements">
                    <li class="text-muted" x-text="req"></li>
                  </template>
                </ul>
              </div>
            </div>

            <!-- Price & Selection Actions -->
            <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
              <div>
                <span class="text-muted d-block small">Biaya Pendaftaran:</span>
                <strong class="text-primary-950 text-lg" x-text="formatCurrency(track.fee)"></strong>
              </div>
              <div>
                <!-- Active Button -->
                <button class="btn btn-success text-white px-4 py-2 fw-bold" 
                  x-show="!isTrackDisabled(track) && isRecommended(track)"
                  @click="selectTrack(track)">
                  <i class="fa-solid fa-rotate-right me-1"></i> Re-Apply <i class="fa-solid fa-chevron-right ms-1 fs-xs"></i>
                </button>
                <button class="btn btn-primary px-4 py-2" 
                  x-show="!isTrackDisabled(track) && !isRecommended(track)"
                  @click="selectTrack(track)">
                  Pilih Jalur <i class="fa-solid fa-chevron-right ms-2 fs-xs"></i>
                </button>

                <!-- Disabled Warning Button (rejected) -->
                <button class="btn btn-outline-danger border-0 cursor-pointer px-3 py-2 text-start" 
                  style="max-width: 180px; font-size:0.75rem;"
                  x-show="isRejectedTrack(track)"
                  @click="toastr.error('Anda tidak dapat mendaftar ulang pada jalur yang sama (' + track.name + '). Silakan pilih jalur alternatif.')"
                  title="Jalur Tidak Tersedia">
                  <i class="fa-solid fa-ban me-1"></i> Tidak Tersedia
                </button>

                <!-- Disabled Warning Button (quota / other) -->
                <button class="btn btn-secondary bg-light text-muted border-0 cursor-pointer px-3 py-2 text-start" 
                  style="max-width: 180px; font-size:0.75rem;"
                  x-show="isTrackDisabled(track) && !isRejectedTrack(track)"
                  @click="toastr.warning(getDisabledReason(track))"
                  title="Jalur Tidak Tersedia">
                  <i class="fa-solid fa-lock me-1"></i> <span x-text="isQuotaFull(track) ? 'Kuota Habis' : 'Dinonaktifkan'"></span>
                </button>
              </div>
            </div>

          </div>
        </div>
      </div>
    </template>

    <!-- Empty State if no tracks match filters -->
    <div class="col-12 text-center py-5" x-show="filteredTracks.length === 0" x-transition style="display: none;">
      <div class="text-muted mb-2"><i class="fa-regular fa-compass fs-1"></i></div>
      <h6 class="fw-bold text-primary-900">Jalur tidak ditemukan</h6>
      <p class="text-muted small mb-0">Silakan ubah filter pencarian Anda atau periksa kembali ejaan.</p>
    </div>
  </div>

  <!-- HELPFUL TIPS SIDEBAR CARD -->
  <div class="card shadow-sm border-0 mb-4" data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
    <div class="card-body p-4">
      <h6 class="fw-bold text-primary-800 text-uppercase tracking-wider mb-3">
        <i class="fa-solid fa-circle-question me-2 text-primary-600"></i> Pertanyaan Umum Re-Apply
      </h6>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="p-3 bg-light rounded-md border h-100">
            <h6 class="fw-bold text-primary-900 mb-2 small"><i class="fa-solid fa-arrows-rotate me-2 text-primary-500"></i>Apakah data saya perlu diisi ulang?</h6>
            <p class="text-muted small mb-0">Tidak. Data profil dasar Anda (Nama, NISN, No HP, Sekolah Asal) akan otomatis terbawa ke formulir pendaftaran jalur baru. Anda hanya perlu mengisi data spesifik jalur yang dipilih.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 bg-light rounded-md border h-100">
            <h6 class="fw-bold text-primary-900 mb-2 small"><i class="fa-solid fa-money-bill-wave me-2 text-primary-500"></i>Apakah biaya pendaftaran sebelumnya hangus?</h6>
            <p class="text-muted small mb-0">Ya, biaya pendaftaran jalur sebelumnya tidak dapat dialihkan. Anda perlu membayar biaya registrasi baru sesuai tarif jalur yang dipilih.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 bg-light rounded-md border h-100">
            <h6 class="fw-bold text-primary-900 mb-2 small"><i class="fa-solid fa-calendar-days me-2 text-primary-500"></i>Berapa kali saya bisa re-apply?</h6>
            <p class="text-muted small mb-0">Anda dapat melakukan re-apply maksimal <strong>1 kali</strong> per tahun ajaran. Pastikan Anda memilih jalur yang paling sesuai dengan kemampuan Anda.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CONFIRMATION RE-APPLY MODAL -->
  <div class="glass-modal-backdrop" x-show="showModal" style="display: none;" x-transition>
    <div class="glass-modal-content p-4" @click.outside="showModal = false">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary-900 mb-0"><i class="fa-solid fa-rotate-right text-primary-600 me-2"></i> Konfirmasi Pendaftaran Ulang</h5>
        <button class="btn-close btn btn-sm" @click="showModal = false"></button>
      </div>
      
      <template x-if="selectedTrack">
        <div class="modal-body p-0 mb-4">
          <p class="text-muted small">Anda akan mendaftar ulang pada jalur pendaftaran berikut sebagai pengganti jalur sebelumnya yang ditolak:</p>
          
          <!-- Previous Track (Rejected) -->
          <div class="p-3 bg-danger bg-opacity-10 rounded-md border border-danger border-opacity-25 mb-3">
            <div class="d-flex align-items-center mb-1">
              <i class="fa-solid fa-circle-xmark text-error me-2"></i>
              <span class="fw-bold text-error small">Jalur Sebelumnya (Ditolak):</span>
            </div>
            <span class="text-muted small ms-4">{{ $lastEnrollment->spmbTrack->trackType->name }}</span>
          </div>

          <!-- New Track (Selected) -->
          <div class="p-3 bg-success bg-opacity-10 rounded-md border border-success border-opacity-25 mb-3">
            <div class="d-flex align-items-center mb-2">
              <span class="fs-4 me-2" x-text="selectedTrack.icon"></span>
              <strong class="text-success" x-text="selectedTrack.name"></strong>
            </div>
            <span class="text-muted small d-block mb-1">Rincian Biaya Formulir:</span>
            <strong class="text-primary-950 fs-5" x-text="formatCurrency(selectedTrack.fee)"></strong>
          </div>

          <div class="alert alert-warning p-2 small border border-warning border-opacity-25 mb-3">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Penting:</strong> Setelah konfirmasi, pendaftaran Anda di jalur sebelumnya akan dianggap <strong>final ditolak</strong> dan tidak dapat diulang. Jalur baru akan dikunci sementara untuk pengisian formulir. Biaya registrasi dihitung berdasarkan jalur yang baru dipilih.
          </div>

          <!-- Terms and Conditions checkbox -->
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="reApplyTermsCheck" x-model="agreedToTerms">
            <label class="form-check-label text-muted small cursor-pointer" for="reApplyTermsCheck">
              Saya memahami bahwa pendaftaran ulang bersifat <strong>final</strong> dan menyetujui persyaratan pendaftaran pada <span class="fw-bold" x-text="selectedTrack.name"></span> serta bersedia mengunggah dokumen yang valid.
            </label>
          </div>
        </div>
      </template>

      <div class="d-flex gap-2 justify-content-end">
        <button class="btn btn-sm btn-outline-secondary px-3 py-2 border-primary text-primary-600" @click="showModal = false">Batal</button>
        <button class="btn btn-sm btn-success px-4 py-2 fw-bold" 
          :disabled="!agreedToTerms" 
          @click="confirmReApply()"><i class="fa-solid fa-rotate-right me-1"></i> Konfirmasi Re-Apply <i class="fa-solid fa-chevron-right ms-1 fs-xs"></i></button>
      </div>
    </div>
  </div>

</div>
@endsection

@push('js')
<script>
  function reApplyApp() {
    return {
      mobileMenuOpen: false,
      searchQuery: '',
      selectedCategory: 'all',
      rejectedTrackId: @json($lastEnrollment->spmb_track_id),
      selectedTrack: null,
      showModal: false,
      agreedToTerms: false,

      // Generated dynamically from DB
      tracks: {!! json_encode($tracks->map(function($track) {
          $quotaUsed = $track->enrollments_count;
          
          $category = 'test';
          $nameLower = strtolower($track->trackType->name);
          if (str_contains($nameLower, 'prestasi') || str_contains($nameLower, 'umum') || str_contains($nameLower, 'berkas') || str_contains($nameLower, 'reguler')) {
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

          $requirements = [];
          foreach ($track->assessments as $assess) {
              $requirements[] = 'Mengikuti seleksi ' . $assess->assessmentType->name;
          }
          $requirements[] = 'Upload Pasfoto, KK & Berkas Pendukung';
          $requirements[] = 'Nomor Induk Siswa Nasional (NISN) aktif';

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
        return Math.max(0, track.quotaMax - track.quotaUsed);
      },

      isQuotaFull(track) {
        return this.getQuotaLeft(track) <= 0;
      },

      isRejectedTrack(track) {
        return track.id === this.rejectedTrackId;
      },

      isRecommended(track) {
        const rejectedIsPrestasi = '{{ str_contains(strtolower($lastEnrollment->spmbTrack->trackType->name), "prestasi") ? "true" : "false" }}' === 'true';
        if (rejectedIsPrestasi) {
          return track.name.toLowerCase().includes('cbt') || track.name.toLowerCase().includes('tes') || track.name.toLowerCase().includes('reguler');
        }
        return false;
      },

      isTrackDisabled(track) {
        if (this.isRejectedTrack(track)) return true;
        return this.isQuotaFull(track);
      },

      getDisabledReason(track) {
        if (this.isRejectedTrack(track)) {
          return 'Anda telah ditolak pada jalur ini (' + track.name + '). Silakan pilih jalur alternatif.';
        }
        if (this.isQuotaFull(track)) {
          return 'Kuota pendaftaran jalur ini sudah penuh (Habis)';
        }
        return '';
      },

      selectTrack(track) {
        if (this.isTrackDisabled(track)) return;
        this.selectedTrack = track;
        this.agreedToTerms = false;
        this.showModal = true;
      },

      confirmReApply() {
        if (!this.agreedToTerms || !this.selectedTrack) return;
        this.showModal = false;
        
        // Post submit to Laravel route
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("portal.enrollment.reapply-submit", ":id") }}'.replace(':id', this.selectedTrack.id);
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
      },

      formatCurrency(amount) {
        if (amount <= 0) return 'Gratis';
        return 'Rp ' + amount.toLocaleString('id-ID');
      }
    }
  }
</script>
@endpush
