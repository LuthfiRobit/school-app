@extends('admin.layouts.auth')

@section('title', 'Login')

@section('content')
<div class="card my-5">
    <div class="card-body">
        <div class="text-center">
            <a href="#"><img src="{{ asset('template/admin/dist/assets/images/logo-dark.svg') }}" alt="img"></a>
            <div class="d-grid my-3">
                <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('template/admin/dist/assets/images/authentication/facebook.svg') }}" alt="img"> <span>
                        Sign In with Facebook</span>
                </button>
                <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('template/admin/dist/assets/images/authentication/twitter.svg') }}" alt="img"> <span> Sign
                        In with Twitter</span>
                </button>
                <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('template/admin/dist/assets/images/authentication/google.svg') }}" alt="img"> <span> Sign
                        In with Google</span>
                </button>
            </div>
        </div>
        <div class="saprator my-3">
            <span>OR</span>
        </div>
        <h4 class="text-center f-w-500 mb-3">Login with your email</h4>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 fs-7">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group mb-3">
                <input type="text" name="login" class="form-control @error('login') is-invalid @enderror" id="floatingInput" placeholder="Email or Username" value="{{ old('login') }}" required autofocus>
            </div>
            <div class="form-group mb-3">
                <input type="password" name="password" class="form-control @error('login') is-invalid @enderror" id="floatingInput1" placeholder="Password" required>
            </div>
            <div class="d-flex mt-1 justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" name="remember">
                    <label class="form-check-label text-muted" for="customCheckc1">Remember me?</label>
                </div>
                <a href="#"><h6 class="text-secondary f-w-400 mb-0">Forgot Password?</h6></a>
            </div>
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-end mt-4">
            <h6 class="f-w-500 mb-0">Don't have an Account?</h6>
            <a href="#" class="link-primary">Create Account</a>
        </div>
    </div>
</div>
@endsection
