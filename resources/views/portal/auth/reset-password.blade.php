@extends('portal.layouts.auth')

@section('title', 'Atur Ulang Password')

@section('content')
<div x-data="{ loading: false }">
  <div class="text-center mb-4">
    <div class="mb-3 d-flex justify-content-center">
      <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; background-color: var(--color-primary-50); box-shadow: 0 0 0 8px rgba(13, 71, 161, 0.05);">
        <i class="fa-solid fa-lock-open text-primary-600 fs-3"></i>
      </div>
    </div>
    <h2 class="auth-title">Atur Ulang Password</h2>
    <p class="auth-subtitle">Masukkan password baru Anda untuk memulihkan akses ke akun portal.</p>
  </div>

  <form action="{{ route('applicant.password.update') }}" method="POST" @submit="loading = true" x-data="{ showPassword: false }">
    @csrf
    
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
      <label for="email" class="form-label">Alamat Email</label>
      <div class="input-group">
        <span class="input-group-text border-end-0 @error('email') border-danger @enderror"><i class="fa-regular fa-envelope"></i></span>
        <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" id="email" value="{{ $email ?? old('email') }}" placeholder="nama@email.com" required readonly>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password Baru <span class="text-error">*</span></label>
      <div class="input-group">
        <span class="input-group-text border-end-0 @error('password') border-danger @enderror"><i class="fa-solid fa-lock"></i></span>
        <input :type="showPassword ? 'text' : 'password'" name="password" class="form-control border-start-0 border-end-0 px-0 @error('password') is-invalid @enderror" id="password" placeholder="Masukkan password baru" required autofocus>
        <button class="btn btn-outline-secondary border-start-0 bg-transparent" type="button" @click="showPassword = !showPassword">
          <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
        </button>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="mb-4">
      <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-error">*</span></label>
      <div class="input-group">
        <span class="input-group-text border-end-0"><i class="fa-solid fa-lock"></i></span>
        <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" class="form-control border-start-0 px-0" id="password_confirmation" placeholder="Ulangi password baru" required>
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 mt-2" :disabled="loading">
      <span x-show="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
      <span x-show="!loading">Simpan Password Baru</span>
    </button>
  </form>
</div>

<div class="text-center mt-4 pt-3 border-top">
  <a href="{{ route('applicant.login') }}" class="auth-link text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Masuk</a>
</div>
@endsection
