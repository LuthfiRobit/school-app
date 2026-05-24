@extends('portal.layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div x-data="{ loading: false }">
  <div class="text-center mb-4">
    <div class="mb-3 d-flex justify-content-center">
      <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; background-color: var(--color-primary-50); box-shadow: 0 0 0 8px rgba(13, 71, 161, 0.05);">
        <i class="fa-solid fa-key text-primary-600 fs-3"></i>
      </div>
    </div>
    <h2 class="auth-title">Lupa Password?</h2>
    <p class="auth-subtitle">Masukkan alamat email terdaftar, kami akan mengirimkan instruksi untuk reset password Anda.</p>
  </div>

  @if (session('status'))
    <div class="alert alert-success d-flex align-items-center mb-4">
      <i class="fa-solid fa-envelope-circle-check text-success me-3 fs-4"></i>
      <div>
        {{ session('status') }}
      </div>
    </div>
  @endif

  <form action="{{ route('applicant.password.email') }}" method="POST" @submit="loading = true">
    @csrf
    <div class="mb-4">
      <label for="email" class="form-label">Alamat Email</label>
      <div class="input-group">
        <span class="input-group-text border-end-0 @error('email') border-danger @enderror"><i class="fa-regular fa-envelope"></i></span>
        <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 mt-2" :disabled="loading">
      <span x-show="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="display: none;"></span>
      <span x-show="!loading">Kirim Tautan Pemulihan</span>
    </button>
  </form>
</div>

<div class="text-center mt-4 pt-3 border-top">
  <a href="{{ route('applicant.login') }}" class="auth-link text-muted"><i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Masuk</a>
</div>
@endsection
