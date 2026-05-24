@extends('portal.layouts.auth')

@section('title', 'Masuk Portal Calon Siswa')

@section('content')
<div class="mb-4">
  <h2 class="auth-title">Selamat Datang</h2>
  <p class="auth-subtitle">Masuk ke portal pendaftaran Anda</p>
</div>

<form action="{{ route('applicant.login.submit') }}" method="POST" x-data="{ showPassword: false, loading: false }" @submit="loading = true">
  @csrf
  
  <div class="mb-3">
    <label for="login" class="form-label">Alamat Email / Username</label>
    <div class="input-group">
      <span class="input-group-text border-end-0 @error('login') border-danger @enderror"><i class="fa-regular fa-envelope"></i></span>
      <input type="text" name="login" class="form-control border-start-0 ps-0 @error('login') is-invalid @enderror" id="login" value="{{ old('login') }}" placeholder="nama@email.com atau username" required autofocus>
      @error('login')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="mb-4">
    <div class="d-flex justify-content-between mb-1">
      <label for="password" class="form-label mb-0">Password</label>
      <a href="{{ route('applicant.password.request') }}" class="auth-link text-primary-600">Lupa password?</a>
    </div>
    <div class="input-group">
      <span class="input-group-text border-end-0 @error('password') border-danger @enderror"><i class="fa-solid fa-lock"></i></span>
      <input :type="showPassword ? 'text' : 'password'" name="password" class="form-control border-start-0 border-end-0 px-0 @error('password') is-invalid @enderror" id="password" placeholder="Masukkan password" required>
      <button class="btn btn-outline-secondary border-start-0 bg-transparent" type="button" @click="showPassword = !showPassword">
        <i class="fa-regular" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
      </button>
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>
  </div>

  <div class="mb-4 form-check">
    <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
    <label class="form-check-label text-muted" for="rememberMe">Ingat sesi saya</label>
  </div>

  <button type="submit" class="btn btn-primary w-100 py-2" :disabled="loading">
    <span x-show="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
    <span x-show="!loading">Masuk ke Portal</span>
  </button>
</form>

<div class="text-center mt-4 pt-3 border-top">
  <p class="text-muted mb-0">Belum memiliki akun? <a href="{{ route('applicant.register') }}" class="auth-link text-secondary-600">Buat Akun Baru</a></p>
</div>
@endsection
