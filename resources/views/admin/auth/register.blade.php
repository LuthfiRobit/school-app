@extends('admin.layouts.auth')

@section('title', 'Register')

@section('content')
<div class="card my-5">
    <div class="card-body">
        <div class="text-center">
            <a href="#"><img src="{{ asset('template/admin/dist/assets/images/logo-dark.svg') }}" alt="img"></a>
            <div class="d-grid my-3">
                <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('template/admin/dist/assets/images/authentication/facebook.svg') }}" alt="img"> <span>
                        Sign Up with Facebook</span>
                </button>
                <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('template/admin/dist/assets/images/authentication/twitter.svg') }}" alt="img"> <span> Sign
                        Up with Twitter</span>
                </button>
                <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('template/admin/dist/assets/images/authentication/google.svg') }}" alt="img"> <span> Sign
                        Up with Google</span>
                </button>
            </div>
        </div>
        <div class="saprator my-3">
            <span>OR</span>
        </div>
        <h4 class="text-center f-w-500 mb-3">Sign up with your work email.</h4>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <input type="text" name="first_name" class="form-control" placeholder="First Name" required autofocus>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            </div>
            <div class="form-group mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="form-group mb-3">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
            </div>
            <div class="d-flex mt-1 justify-content-between">
                <div class="form-check">
                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1" name="terms" required>
                    <label class="form-check-label text-muted" for="customCheckc1">I agree to all the Terms & Condition</label>
                </div>
            </div>
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Sign up</button>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-end mt-4">
            <h6 class="f-w-500 mb-0">Already have an Account?</h6>
            <a href="{{ route('login') }}" class="link-primary">Login</a>
        </div>
    </div>
</div>
@endsection
