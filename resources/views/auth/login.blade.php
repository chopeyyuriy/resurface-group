@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Login')
@endsection

@section('css')
    <!-- owl.carousel css -->
    <link rel="stylesheet" href="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.css') }}">
@endsection

@section('body')

    <body class="auth-body-bg">
    @endsection

    @section('content')

    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="" style="background-color: #2e3343;">
                            <div class="row">
                                <div class="col-12" style="padding: 2.5rem 5rem;">
                                    <img src="{{ URL::asset('/assets/images/logo-light-new2.png') }}" width="100%">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0" style="padding-top: 1rem!important;">
                            <div class="p-2">
                                <form class="form-horizontal" method="POST" action="{{ route('auth') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Email</label>
                                        <input name="email" type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" id="username"
                                            placeholder="Enter Email" autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <div
                                            class="input-group auth-pass-inputgroup @error('password') is-invalid @enderror">
                                            <input type="password" name="password"
                                                class="form-control  @error('password') is-invalid @enderror"
                                                id="userpassword" value="" placeholder="Enter password"
                                                aria-label="Password" aria-describedby="password-addon">
                                            <button class="btn btn-light " type="button" id="password-addon"><i
                                                    class="mdi mdi-eye-outline"></i></button>
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-check">
                                        <input name="remember" value="1" class="form-check-input" type="checkbox" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>

                                    <div class="mt-3 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light"
                                            type="submit">Log In</button>
                                    </div>

                                    @if (Route::has('password.request'))
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('password.request') }}" class="text-muted"><i
                                                class="mdi mdi-lock me-1"></i> Forgot your password?</a>
                                    </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <div>
                            <p><a href="{{ route('forgot') }}" class="fw-medium text-primary">Forgot password ?</a></p>
                            <p>© <script>document.write(new Date().getFullYear())</script> The Resurface Group.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection
    @section('script')
        <!-- owl.carousel js -->
        <script src="{{ URL::asset('/assets/libs/owl.carousel/owl.carousel.min.js') }}"></script>
        <!-- auth-2-carousel init -->
        <script src="{{ URL::asset('/assets/js/pages/auth-2-carousel.init.js') }}"></script>
    @endsection
