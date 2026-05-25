@extends('portal.layouts.app')

@section('title', 'Formulir Pendaftaran')

@push('css')
<style>
  /* Premium file upload card styling */
  .upload-dropzone {
    border: 2px dashed var(--color-divider);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    text-align: center;
    background-color: var(--color-surface);
    transition: all var(--transition-base);
    cursor: pointer;
    position: relative;
  }
  .upload-dropzone:hover {
    border-color: var(--color-primary-400);
    background-color: var(--color-primary-50);
  }
  .upload-dropzone input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
  }
  .file-preview-card {
    border: 1px solid var(--color-divider);
    border-radius: var(--radius-md);
    padding: 0.75rem;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.75rem;
    box-shadow: var(--shadow-xs);
  }
  .preview-thumbnail {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-sm);
    object-fit: cover;
    border: 1px solid var(--color-divider);
    margin-right: 0.75rem;
  }
  .save-badge-container {
    display: inline-flex;
    align-items: center;
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.25rem 0.6rem;
    border-radius: var(--radius-full);
    transition: all 0.3s ease;
  }
  .save-badge-saved {
    background-color: rgba(40, 167, 69, 0.1);
    color: var(--color-success);
    border: 1px solid rgba(40, 167, 69, 0.2);
  }
  .save-badge-saving {
    background-color: rgba(11, 74, 111, 0.1);
    color: var(--color-primary-700);
    border: 1px solid rgba(11, 74, 111, 0.2);
  }
  .save-badge-changed {
    background-color: rgba(242, 153, 74, 0.1);
    color: var(--color-warning-800);
    border: 1px solid rgba(242, 153, 74, 0.2);
  }
</style>
@endpush

@section('content')
<div x-data="pendaftaranFormApp()" class="w-100">

  <!-- Page Header & Breadcrumb -->
  <div class="mb-4" data-aos="fade-up" data-aos-duration="600">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2 small">
        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}" class="text-primary-600 text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('portal.enrollment.select-track') }}" class="text-primary-600 text-decoration-none">Pilih Jalur</a></li>
        <li class="breadcrumb-item active" aria-current="page">Formulir Pendaftaran</li>
      </ol>
    </nav>
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
      <div>
        <h4 class="fw-bold text-primary-900 mb-1">Formulir Pendaftaran Online</h4>
        <p class="text-muted mb-0">Lengkapi berkas dan isian data secara bertahap untuk jalur yang telah Anda tentukan.</p>
      </div>
      <div class="mt-3 mt-md-0 d-flex align-items-center gap-3">
        <!-- Draft Auto-save Indicator Badge -->
        <div class="save-badge-container" :class="saveStatus === 'saved' ? 'save-badge-saved' : (saveStatus === 'saving' ? 'save-badge-saving' : 'save-badge-changed')">
          <i class="fa-solid fa-circle-notch fa-spin me-2" x-show="saveStatus === 'saving'"></i>
          <i class="fa-regular fa-floppy-disk me-2" x-show="saveStatus === 'saved'"></i>
          <i class="fa-solid fa-pen-to-square me-2" x-show="saveStatus === 'changed'"></i>
          <span x-text="saveMessage"></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Layout Grid -->
  <form id="enrollmentForm" action="{{ route('portal.enrollment.submit', $track->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">

      <!-- LEFT COLUMN: MULTI-STEP FORM -->
      <div class="col-lg-8" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
        
        <!-- STEP INDICATOR WIDGET -->
        <div class="card p-3 mb-4 shadow-sm border-0">
          <div class="position-relative mx-auto mt-2 mb-2" style="max-width: 480px; min-width: 280px;">
            <!-- Line connector -->
            <div class="position-absolute translate-middle-y" style="height: 3px; left: 32px; right: 32px; top: 16px; z-index: 0;">
              <div class="position-absolute top-0 start-0 w-100 bg-secondary-subtle" style="height: 3px;"></div>
              <div class="position-absolute top-0 start-0 bg-success transition-all" style="height: 3px; transition: width 0.4s ease;"
                :style="'width: ' + ((step - 1) * 50) + '%'"></div>
            </div>

            <!-- Indicator circles -->
            <div class="d-flex justify-content-between position-relative" style="z-index: 1;">
              <!-- Step 1 -->
              <div class="text-center" style="width: 64px;">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
                  :class="step > 1 ? 'bg-success text-white border-success' : (step === 1 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle')"
                  style="width: 32px; height: 32px;">
                  <i class="fa-solid fa-check" x-show="step > 1"></i>
                  <span x-show="step === 1">1</span>
                </div>
                <span class="small fw-semibold d-block text-truncate" :class="step === 1 ? 'text-primary' : 'text-secondary'">Profil</span>
              </div>

              <!-- Step 2 -->
              <div class="text-center" style="width: 64px;">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
                  :class="step > 2 ? 'bg-success text-white border-success' : (step === 2 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle')"
                  style="width: 32px; height: 32px;">
                  <i class="fa-solid fa-check" x-show="step > 2"></i>
                  <span x-show="step <= 2">2</span>
                </div>
                <span class="small fw-semibold d-block text-truncate" :class="step === 2 ? 'text-primary' : 'text-secondary'">Form Jalur</span>
              </div>

              <!-- Step 3 -->
              <div class="text-center" style="width: 64px;">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2 transition-all"
                  :class="step === 3 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle'"
                  style="width: 32px; height: 32px;">
                  <span x-show="step <= 3">3</span>
                </div>
                <span class="small fw-semibold d-block text-truncate" :class="step === 3 ? 'text-primary' : 'text-secondary'">Berkas</span>
              </div>
            </div>
          </div>
        </div>

        @if ($errors->any())
          <div class="alert alert-danger p-3 mb-4 rounded-md">
            <h6 class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation"></i> Terjadi Kesalahan Validasi:</h6>
            <ul class="mb-0 ps-3">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <!-- FORM CARD -->
        <div class="card shadow-sm border-0 p-4">
          
          <!-- STEP 1: PROFIL DASAR -->
          <div x-show="step === 1" x-transition.opacity>
            <h5 class="fw-bold text-primary-900 border-bottom pb-2 mb-4"><i class="fa-regular fa-address-card me-2 text-primary-600"></i> Profil Calon Siswa</h5>
            
            <div class="alert alert-info border border-info border-opacity-25 p-3 rounded-md mb-4 d-flex align-items-start">
              <i class="fa-solid fa-circle-info text-primary-600 fs-5 mt-1 me-3"></i>
              <div class="small">
                <strong>Data Profil Terkunci:</strong> Kolom profil dasar di bawah diimpor langsung dari akun registrasi Anda. Jika terjadi kesalahan data, silakan hubungi bagian administrasi institusi.
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label text-primary-800">Nama Lengkap</label>
                <input type="text" class="form-control bg-light text-muted" value="{{ $applicant->full_name }}" readonly disabled>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-primary-800">NISN (Nomor Induk Siswa Nasional)</label>
                <input type="text" class="form-control bg-light text-muted" value="{{ $applicant->nisn }}" readonly disabled>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label text-primary-800">Nomor Telepon/WhatsApp</label>
                <input type="text" class="form-control bg-light text-muted" value="{{ $applicant->phone }}" readonly disabled>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-primary-800">Jenis Kelamin</label>
                <input type="text" class="form-control bg-light text-muted" value="{{ $applicant->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}" readonly disabled>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label text-primary-800">Tempat, Tanggal Lahir</label>
                <input type="text" class="form-control bg-light text-muted" value="{{ $applicant->place_of_birth }}, {{ $applicant->date_of_birth ? $applicant->date_of_birth->translatedFormat('d F Y') : '' }}" readonly disabled>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label text-primary-800">Agama</label>
                <input type="text" class="form-control bg-light text-muted" value="{{ $applicant->religion }}" readonly disabled>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
              <button type="button" class="btn btn-primary px-4 py-2" @click="nextStep()">
                Simpan & Lanjutkan <i class="fa-solid fa-arrow-right ms-2 fs-xs"></i>
              </button>
            </div>
          </div>

          <!-- STEP 2: FORM DINAMIS KHUSUS JALUR -->
          <div x-show="step === 2" x-transition.opacity style="display: none;">
            <h5 class="fw-bold text-primary-900 border-bottom pb-2 mb-4">
              <i class="fa-solid fa-sliders me-2 text-primary-600"></i> Formulir Khusus: <span class="text-primary-700">{{ $track->trackType->name }}</span>
            </h5>

            @if($formFields->where('field_type', '!=', 'file')->count() === 0)
              <div class="alert alert-info border border-info border-opacity-25 p-3 rounded-md mb-0">
                <i class="fa-solid fa-circle-info text-primary-600 me-2"></i>
                Tidak ada formulir khusus tambahan untuk jalur ini. Silakan klik tombol di bawah untuk melanjutkan ke pengunggahan berkas.
              </div>
            @else
              @foreach($formFields->where('field_type', '!=', 'file') as $field)
                @include('portal.enrollment.partials.dynamic-field', ['field' => $field, 'formData' => $formData])
              @endforeach
            @endif

            <!-- Wizard Navigation -->
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
              <button type="button" class="btn btn-outline-secondary px-4 py-2 border-primary text-primary-600" @click="prevStep()">
                <i class="fa-solid fa-arrow-left me-2 fs-xs"></i> Sebelumnya
              </button>
              <button type="button" class="btn btn-primary px-4 py-2" @click="nextStep()" :disabled="!isStepValid(2)">
                Simpan & Lanjutkan <i class="fa-solid fa-arrow-right ms-2 fs-xs"></i>
              </button>
            </div>
          </div>

          <!-- STEP 3: UNGGAH BERKAS PENDUKUNG -->
          <div x-show="step === 3" x-transition.opacity style="display: none;">
            <h5 class="fw-bold text-primary-900 border-bottom pb-2 mb-4"><i class="fa-solid fa-cloud-arrow-up me-2 text-primary-600"></i> Pengunggahan Dokumen Wajib</h5>
            
            <p class="text-muted small mb-4">Silakan unggah dokumen yang disyaratkan di bawah ini. File harus bertipe PDF, JPG, JPEG, atau PNG dengan ukuran maksimal sesuai batas tiap berkas.</p>

            @if($formFields->where('field_type', 'file')->count() === 0)
              <div class="alert alert-info border border-info border-opacity-25 p-3 rounded-md mb-4 text-center">
                <i class="fa-solid fa-circle-info text-primary-600 me-2"></i>
                Tidak ada dokumen / berkas khusus yang wajib diunggah untuk jalur pendaftaran ini.
              </div>
            @else
              @foreach($formFields->where('field_type', 'file') as $field)
                @include('portal.enrollment.partials.dynamic-field', ['field' => $field, 'formData' => $formData])
              @endforeach
            @endif

            <!-- Submit Check Alert -->
            <div class="alert alert-warning border border-warning border-opacity-25 p-3 rounded-md mb-4 d-flex align-items-start" x-show="isStepValid(3)">
              <i class="fa-solid fa-triangle-exclamation text-warning fs-5 mt-1 me-3"></i>
              <div class="small">
                <strong>Verifikasi Akhir:</strong> Dengan mengirimkan pendaftaran ini, Anda menyatakan bahwa seluruh isian data dan dokumen yang diunggah adalah sah, asli, dan valid. Kesalahan penulisan data dapat menyebabkan pembatalan berkas.
              </div>
            </div>

            <!-- Wizard Navigation -->
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
              <button type="button" class="btn btn-outline-secondary px-4 py-2 border-primary text-primary-600" @click="prevStep()">
                <i class="fa-solid fa-arrow-left me-2 fs-xs"></i> Sebelumnya
              </button>
              <button type="button" class="btn btn-success px-4 py-2" :disabled="!isStepValid(3)" @click="submitForm()">
                <i class="fa-solid fa-paper-plane me-2 fs-xs"></i> Kirim Pendaftaran
              </button>
            </div>
          </div>

        </div>
      </div>

      <!-- RIGHT COLUMN: SIDEBAR SUMMARY -->
      <div class="col-lg-4" data-aos="fade-left" data-aos-duration="600" data-aos-delay="150">
        
        <!-- TRACK INFORMATION SUMMARY CARD -->
        <div class="card mb-4 shadow-sm border-0">
          <div class="card-body p-4">
            <h6 class="fw-bold text-primary-800 text-uppercase tracking-wider mb-3"><i class="fa-solid fa-graduation-cap me-2"></i> Pilihan Jalur</h6>
            
            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-md border">
              <div class="rounded bg-primary bg-opacity-10 text-primary-700 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 1.6rem;">
                <span>🎓</span>
              </div>
              <div>
                <strong class="text-primary-900 d-block">{{ $track->trackType->name }}</strong>
                <span class="text-muted small">Tahun Ajaran {{ $track->spmbConfiguration->academicYear->name ?? '' }}</span>
              </div>
            </div>

            <div class="border-top pt-3">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Biaya Formulir:</span>
                <strong class="text-primary-900 small" x-text="formatCurrency({{ (float)$track->registration_fee }})"></strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted small">Status Pengisian:</span>
                <span class="fw-bold text-primary-800 small" x-text="filledRequiredFields + ' dari ' + totalRequiredFields + ' Kolom'"></span>
              </div>
              <div class="mt-3">
                <div class="progress" style="height: 8px; border-radius: 4px;">
                  <div class="progress-bar bg-primary" role="progressbar" :style="'width: ' + filledPercentage + '%'" :aria-valuenow="filledPercentage" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="text-end text-muted small mt-1" x-text="filledPercentage + '% Lengkap'"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- PROFILE BRIEF -->
        <div class="card mb-4 shadow-sm border-0">
          <div class="card-body p-4">
            <h6 class="fw-bold text-primary-800 text-uppercase tracking-wider mb-3"><i class="fa-regular fa-user me-2"></i> Biodata Pendaftar</h6>
            <div class="d-flex align-items-center mb-3">
              <div class="portal-avatar me-3" style="width: 44px; height: 44px; font-size: 1.2rem;">AS</div>
              <div>
                <h6 class="fw-bold text-primary-900 mb-0">{{ $applicant->full_name }}</h6>
                <small class="text-muted">No. Reg: {{ $enrollment->enrollment_number ?? 'Belum Terdaftar' }}</small>
              </div>
            </div>
            <div class="border-top pt-3">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted small">NISN:</span>
                <span class="fw-bold text-primary-900 small">{{ $applicant->nisn }}</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted small">WhatsApp:</span>
                <span class="fw-bold text-primary-900 small">{{ $applicant->phone }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>
  </form>

</div>
@endsection

@push('js')
@include('portal.enrollment.partials.scripts', ['track' => $track, 'formData' => $formData, 'formFields' => $formFields])
@endpush
