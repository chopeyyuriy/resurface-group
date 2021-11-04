@extends('layouts.master-without-nav')

@section('title')
    @lang('translation.Recover_Password') 2
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
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary">Reset Password</h5>
                                        <p>Enter your Email and instructions will be sent to you!</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ URL::asset('/assets/images/profile-img.png') }}" alt=""
                                        class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="auth-logo">
                                <div class="auth-logo-light">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="{{ URL::asset('/assets/images/logo-light.svg') }}" alt=""
                                                class="rounded-circle" height="34">
                                        </span>
                                    </div>
                                </div>

                                <div class="auth-logo-dark">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="logo-lg">
                                            <img src="{{ URL::asset ('/assets/images/logo-light-new2.png') }}" alt="" height="20">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                            @if (session('status'))
                                <div class="alert alert-success text-center mb-4" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form class="form-horizontal" method="POST" action="{{ route('forgot.reset') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="Enter email"
                                        value="{{ old('email') }}" id="email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-primary w-md waves-effect waves-light"
                                        type="submit">Reset</button>
                                </div>

                            </form>
                            <div class="mt-5 text-center">
                                <p>Remember It ? <a href="{{ route('login') }}"
                                        class="font-weight-medium text-primary"> Sign In here</a> </p>
                            </div>

                            </div>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <div>
                            <p>Don't have an account ? <a href="{{ route('registration.view') }}" class="fw-medium text-primary">
                                    Signup now </a> </p>
                            <p>© <script>
                                    document.write(new Date().getFullYear())

                                </script> The Resurface Group.
                            </p>
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
