@extends('portal.layouts.auth')

@section('title', 'Buat Akun Portal Calon Siswa')

@section('content')
<div class="mb-4 text-center">
  <h2 class="auth-title">Buat Akun Baru</h2>
  <p class="auth-subtitle">Sistem Penerimaan Siswa Baru Tunas Luhur</p>
</div>

<!-- Progress Steps Indicator -->
<div class="position-relative mx-auto mb-4" style="max-width: 220px; min-width: 180px;">
  <!-- 1. Garis Penghubung -->
  <div class="position-absolute translate-middle-y" style="height: 3px; left: 32px; right: 32px; top: 16px; z-index: 0;">
    <div class="position-absolute top-0 start-0 w-100 bg-secondary-subtle" style="height: 3px;"></div>
    <div class="position-absolute top-0 start-0 bg-primary"
      style="height: 3px; transition: width 0.4s ease;"
      :style="'width: ' + (step > 1 ? '100%' : '0%')">
    </div>
  </div>

  <!-- 2. Step Circles -->
  <div class="d-flex justify-content-between position-relative" style="z-index: 1;">
    <!-- Step 1: Akun -->
    <div class="text-center" style="width: 64px;">
      <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2"
        :class="step > 1 ? 'bg-success text-white border-success' : (step === 1 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle')"
        style="width: 32px; height: 32px; transition: all 0.3s ease;">
        <i class="fa-solid fa-check" x-show="step > 1" style="display: none;"></i>
        <span x-show="step === 1">1</span>
      </div>
      <span class="small fw-semibold d-block text-truncate" :class="step === 1 ? 'text-primary' : 'text-secondary'">Akun</span>
    </div>

    <!-- Step 2: Data Diri -->
    <div class="text-center" style="width: 64px;">
      <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold border border-2"
        :class="step > 2 ? 'bg-success text-white border-success' : (step === 2 ? 'bg-primary text-white border-primary shadow' : 'bg-light text-muted border-secondary-subtle')"
        style="width: 32px; height: 32px; transition: all 0.3s ease;">
        <i class="fa-solid fa-check" x-show="step > 2" style="display: none;"></i>
        <span x-show="step <= 2">2</span>
      </div>
      <span class="small fw-semibold d-block text-truncate" :class="step === 2 ? 'text-primary' : 'text-secondary'">Data Diri</span>
    </div>
  </div>
</div>

<form action="{{ route('applicant.register.submit') }}" method="POST" x-data="registerWizard()" @submit="handleSubmit($event)">
  @csrf
  
  <!-- Step 1: Data Akun -->
  <div x-show="step === 1" x-transition.opacity>
    <h5 class="mb-3 fw-bold text-primary-900 border-bottom pb-2">Informasi Akun</h5>
    
    <div class="mb-3">
      <label for="email" class="form-label">Alamat Email <span class="text-error">*</span></label>
      <div class="input-group">
        <span class="input-group-text border-end-0 @error('email') border-danger @enderror"><i class="fa-regular fa-envelope"></i></span>
        <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" id="email" x-model="form.email" placeholder="nama@email.com" required>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-text mt-2"><i class="fa-solid fa-circle-info me-1"></i>Email aktif untuk koordinasi pendaftaran.</div>
    </div>
    
    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Password <span class="text-error">*</span></label>
        <div class="input-group">
          <span class="input-group-text border-end-0 @error('password') border-danger @enderror"><i class="fa-solid fa-lock"></i></span>
          <input :type="showPassword ? 'text' : 'password'" name="password" class="form-control border-start-0 border-end-0 px-0 @error('password') is-invalid @enderror" id="password" x-model="form.password" @input="checkPasswordStrength()" placeholder="Buat password" required>
          <button class="btn btn-outline-secondary bg-transparent border-start-0" type="button" @click="showPassword = !showPassword">
            <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
          </button>
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        
        <!-- Password Strength Meter -->
        <div class="password-strength-meter">
          <div class="strength-bar" :class="strengthClass"></div>
        </div>
        <div class="strength-text" :class="strengthTextColor" x-text="strengthLabel"></div>
      </div>
      
      <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-error">*</span></label>
        <div class="input-group">
          <span class="input-group-text border-end-0"><i class="fa-solid fa-lock"></i></span>
          <input :type="showPasswordConfirm ? 'text' : 'password'" name="password_confirmation" class="form-control border-start-0 border-end-0 px-0" id="password_confirmation" x-model="form.passwordConfirm" placeholder="Ulangi password" required>
          <button class="btn btn-outline-secondary bg-transparent border-start-0" type="button" @click="showPasswordConfirm = !showPasswordConfirm">
            <i class="fa-regular" :class="showPasswordConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
          </button>
        </div>
        <div class="form-text text-error mt-1" x-show="form.password !== form.passwordConfirm && form.passwordConfirm !== ''" style="display: none;">
          <i class="fa-solid fa-triangle-exclamation me-1"></i>Password tidak cocok!
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
      <button type="button" class="btn btn-primary px-4" @click="nextStep()" :disabled="!isStep1Valid">Selanjutnya <i class="fa-solid fa-arrow-right ms-2"></i></button>
    </div>
  </div>

  <!-- Step 2: Data Diri -->
  <div x-show="step === 2" x-transition.opacity style="display: none;">
    <h5 class="mb-3 fw-bold text-primary-900 border-bottom pb-2">Identitas Calon Siswa</h5>
    
    <div class="mb-3">
      <label for="full_name" class="form-label">Nama Lengkap <span class="text-error">*</span></label>
      <div class="input-group">
        <span class="input-group-text border-end-0 @error('full_name') border-danger @enderror"><i class="fa-regular fa-user"></i></span>
        <input type="text" name="full_name" class="form-control border-start-0 ps-0 @error('full_name') is-invalid @enderror" id="full_name" x-model="form.fullName" placeholder="Sesuai ijazah sebelumnya" required>
        @error('full_name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="place_of_birth" class="form-label">Tempat Lahir <span class="text-error">*</span></label>
        <div class="input-group">
          <span class="input-group-text border-end-0 @error('place_of_birth') border-danger @enderror"><i class="fa-solid fa-location-dot"></i></span>
          <input type="text" name="place_of_birth" class="form-control border-start-0 ps-0 @error('place_of_birth') is-invalid @enderror" id="place_of_birth" x-model="form.placeOfBirth" placeholder="Kota Kelahiran" required>
          @error('place_of_birth')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="col-md-6 mb-3">
        <label for="date_of_birth" class="form-label">Tanggal Lahir <span class="text-error">*</span></label>
        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" x-model="form.dateOfBirth" required>
        @error('date_of_birth')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="gender" class="form-label">Jenis Kelamin <span class="text-error">*</span></label>
        <select name="gender" class="form-select form-control @error('gender') is-invalid @enderror" id="gender" x-model="form.gender" required>
          <option value="" disabled selected>Pilih jenis kelamin</option>
          <option value="L">Laki-laki</option>
          <option value="P">Perempuan</option>
        </select>
        @error('gender')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Nomor WhatsApp <span class="text-error">*</span></label>
        <div class="input-group">
          <span class="input-group-text border-end-0 @error('phone') border-danger @enderror"><i class="fa-brands fa-whatsapp"></i></span>
          <input type="text" name="phone" class="form-control border-start-0 ps-0 @error('phone') is-invalid @enderror" id="phone" x-model="form.phone" placeholder="081234567890" required>
          @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="nisn" class="form-label">NISN (Opsional)</label>
        <div class="input-group">
          <span class="input-group-text border-end-0 @error('nisn') border-danger @enderror"><i class="fa-solid fa-id-card"></i></span>
          <input type="text" name="nisn" class="form-control border-start-0 ps-0 @error('nisn') is-invalid @enderror" id="nisn" x-model="form.nisn" placeholder="10 digit nomor NISN">
          @error('nisn')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="col-md-6 mb-3">
        <label for="nik" class="form-label">NIK (Opsional)</label>
        <div class="input-group">
          <span class="input-group-text border-end-0 @error('nik') border-danger @enderror"><i class="fa-solid fa-address-card"></i></span>
          <input type="text" name="nik" class="form-control border-start-0 ps-0 @error('nik') is-invalid @enderror" id="nik" x-model="form.nik" placeholder="16 digit nomor NIK">
          @error('nik')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
    </div>

    <div class="mb-3">
      <label for="religion" class="form-label">Agama <span class="text-error">*</span></label>
      <select name="religion" class="form-select form-control @error('religion') is-invalid @enderror" id="religion" x-model="form.religion" required>
        <option value="" disabled selected>Pilih agama</option>
        <option value="Islam">Islam</option>
        <option value="Kristen">Kristen</option>
        <option value="Katolik">Katolik</option>
        <option value="Hindu">Hindu</option>
        <option value="Buddha">Buddha</option>
        <option value="Konghucu">Konghucu</option>
      </select>
      @error('religion')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
      <button type="button" class="btn btn-outline-secondary px-4" @click="prevStep()" style="border-radius: 12px;"><i class="fa-solid fa-arrow-left me-2"></i> Kembali</button>
      <button type="submit" class="btn btn-primary px-4" :disabled="!isStep2Valid || loading">
        <span x-show="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
        <span x-show="!loading">Daftar Sekarang</span>
      </button>
    </div>
  </div>
</form>

<div class="text-center mt-4">
  <p class="text-muted mb-0">Sudah memiliki akun? <a href="{{ route('applicant.login') }}" class="auth-link text-primary-600">Masuk di sini</a></p>
</div>
@endsection

@push('js')
<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('registerWizard', () => ({
      step: 1,
      showPassword: false,
      showPasswordConfirm: false,
      strengthClass: '',
      strengthLabel: '',
      strengthTextColor: '',
      loading: false,
      form: {
        email: "{{ old('email') }}",
        password: '',
        passwordConfirm: '',
        fullName: "{{ old('full_name') }}",
        placeOfBirth: "{{ old('place_of_birth') }}",
        dateOfBirth: "{{ old('date_of_birth') }}",
        gender: "{{ old('gender') }}",
        phone: "{{ old('phone') }}",
        religion: "{{ old('religion') }}",
        nisn: "{{ old('nisn') }}",
        nik: "{{ old('nik') }}"
      },

      init() {
          // auto-focus on Step 2 if step 1 details are present but errors exist on step 2
          @if ($errors->any())
              @if ($errors->has('email') || $errors->has('password'))
                  this.step = 1;
              @else
                  this.step = 2;
              @endif
          @endif
      },

      get isStep1Valid() {
        return this.form.email !== '' && 
               this.form.password.length >= 8 && 
               this.form.password === this.form.passwordConfirm;
      },

      get isStep2Valid() {
        return this.form.fullName !== '' && 
               this.form.placeOfBirth !== '' && 
               this.form.dateOfBirth !== '' && 
               this.form.gender !== '' && 
               this.form.religion !== '' && 
               this.form.phone !== '';
      },

      nextStep() {
        if(this.isStep1Valid) {
          this.step = 2;
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      },

      prevStep() {
        this.step = 1;
      },

      handleSubmit(e) {
          this.loading = true;
      },

      checkPasswordStrength() {
        const pwd = this.form.password;
        if (pwd.length === 0) {
          this.strengthClass = '';
          this.strengthLabel = '';
          this.strengthTextColor = '';
          return;
        }
        
        let score = 0;
        if (pwd.length >= 8) score++;
        if (/[A-Z]/.test(pwd)) score++;
        if (/[0-9]/.test(pwd)) score++;
        if (/[^A-Za-z0-9]/.test(pwd)) score++;

        if (score <= 1 || pwd.length < 8) {
          this.strengthClass = 'strength-weak';
          this.strengthLabel = 'Lemah (Minimal 8 karakter & kombinasi)';
          this.strengthTextColor = 'text-error';
        } else if (score === 2 || score === 3) {
          this.strengthClass = 'strength-medium';
          this.strengthLabel = 'Sedang (Bagus)';
          this.strengthTextColor = 'text-warning';
        } else {
          this.strengthClass = 'strength-strong';
          this.strengthLabel = 'Kuat';
          this.strengthTextColor = 'text-success';
        }
      }
    }));
  });
</script>
@endpush
