@extends('layouts.main')

@section('title', $rental->name.' – Mechanix D.I.Y.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/rental-details.css') }}">
@endpush


@section('content')

<main class="details-wrap py-4 py-md-5">
    <div class="container">

        <a href="{{ route('rentals') }}" class="details-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Rentals
        </a>

        @php
            $defaultImage = $rental->images->where('is_default',1)->first();
            $defaultPrice = $rental->prices->where('is_default',1)->first();
        @endphp

        <div class="details-card mt-3">
            <div class="row g-4 align-items-center">

                {{-- IMAGE --}}
                <div class="col-lg-6">
                    <div class="details-imgbox">
                        <img
                            src="{{ $defaultImage ? asset('storage/'.$defaultImage->image_path) : asset('assets/images/no-image.png') }}"
                            alt="{{ $rental->name }}"
                        >
                    </div>
                </div>


                {{-- DETAILS --}}
                <div class="col-lg-6">

                    {{-- Title --}}
                    <h1 class="details-title">
                        {{ $rental->name }}
                    </h1>

                    {{-- Description --}}
                    {{-- <p class="details-desc">
                        {{ $rental->description ?? 'No description available.' }}
                    </p> --}}


                    {{-- FEATURES (optional: split by comma or newline) --}}
                    {{-- @if($rental->description) --}}
                        <ul class="details-features">

    @if($rental->description)
        @foreach(explode("\n", $rental->description) as $line)
            @if(trim($line))
                <li>{{ $line }}</li>
            @endif
        @endforeach
    {{-- @endif --}}

    <li>
        Basic hand tools and tool carts are available with bay rentals based on availability.
    </li>

</ul>
                    @endif


                    {{-- PRICE --}}
                    <div class="details-pricebox">
                        {{-- <div class="details-price">
                            @if($defaultPrice)
                                $ {{ $defaultPrice->price }} / {{ $defaultPrice->hours }} hrs
                            @else
                                Contact for price
                            @endif
                        </div> --}}
                        <div class="details-price">
    @if($defaultPrice)

        @if($defaultPrice->hours == 1)
            $ {{ $defaultPrice->price }}/hr
        @else
            $ {{ $defaultPrice->price }} / {{ $defaultPrice->hours }} hrs
        @endif

    @else
        Contact for price
    @endif
</div>

                        <div class="details-note">
                            Product ID: {{ $rental->id }}
                        </div>
                    </div>


                    {{-- BOOK BUTTON --}}
                    <a href="{{ route('booking', ['product_id'=>$rental->id]) }}" class="details-bookbtn">
                        Book Now
                    </a>

                </div>

            </div>
        </div>

    </div>
</main>


{{-- <div class="mx-demo-ribbon" aria-label="Demo Mode">
    DEMO
    <small>Work in progress</small>
</div> --}}
<!-- Routes for JS -->
    <div id="mx-routes" data-login-url="{{ route('popup.login') }}" data-register-url="{{ route('popup.register') }}"></div>

    {{-- Auth Modal --}}
    <div class="modal fade" id="mxAuthModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1f1f1f; color:#fff; border-radius:10px;">

                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Continue to Book</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-white bg-dark" id="loginTab" data-bs-toggle="tab" data-bs-target="#loginTabPane" type="button" role="tab" aria-controls="loginTabPane" aria-selected="true" style="border:none; border-radius:5px 5px 0 0;">
                                Login
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-white bg-dark" id="registerTab" data-bs-toggle="tab" data-bs-target="#registerTabPane" type="button" role="tab" aria-controls="registerTabPane" aria-selected="false" style="border:none; border-radius:5px 5px 0 0;">
                                Register
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="authTabsContent">

                        {{-- LOGIN TAB --}}
                        <div class="tab-pane fade show active" id="loginTabPane" role="tabpanel" aria-labelledby="loginTab">
                            <div id="loginErrorMsg" class="alert alert-danger d-none"></div>

                            <form id="loginFormMain">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-white">Email</label>
                                    <input type="email" class="form-control border-0" name="email" id="loginEmail" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-white">Password</label>
                                    <input type="password" class="form-control border-0" name="password" id="loginPassword" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                </div>

                                <div class="text-end mt-2">
                                    <a href="#" id="forgotPasswordBtn" class="small text-danger btn">
                                        Forgot password?
                                    </a>
                                </div>

                                <button type="submit" class="btn w-100 text-white fw-semibold mt-3" style="background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%); border:2px solid #791218; height:46px; letter-spacing:1px;">
                                    Login
                                </button>
                            </form>
                        </div>

                        {{-- REGISTER TAB --}}
                        <div class="tab-pane fade" id="registerTabPane" role="tabpanel" aria-labelledby="registerTab">
                            <div id="registerErrorMsg" class="alert alert-danger d-none"></div>

                            <form id="registerFormMain">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small text-white">Email</label>
                                        <input type="email" class="form-control border-0 form-control-sm" name="email" id="registerEmail" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Mobile</label>
                                        <input type="text" class="form-control border-0 form-control-sm" name="mobile_no" id="registerMobile" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Password</label>
                                        <input type="password" class="form-control border-0 form-control-sm" name="password" id="registerPassword" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Confirm</label>
                                        <input type="password" class="form-control border-0 form-control-sm" name="password_confirmation" id="registerPasswordConfirm" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn w-100 text-white fw-semibold mt-3" style="background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%); border:2px solid #791218; height:46px; letter-spacing:1px;">
                                    Create Account
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Forgot Password Modal --}}
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="background: linear-gradient(180deg,#1f1f1f,#2a2a2a); border-radius:10px;">
            <div class="modal-content mt-4 p-4" style="background: transparent; border:none;">

                <!-- Step 1: Email -->
                <div id="fpStepEmail">
                    <h6 class="mb-3 text-white">Reset Password</h6>
                    <input type="email" id="fpEmailInput" class="form-control form-control-sm mb-2" placeholder="Enter email" style="background:#2d2d2d;color:#fff;border:none;">
                    <button class="btn btn-primary w-100 btn-sm d-flex align-items-center justify-content-center gap-2" id="fpSendOtpBtn">
                        <span class="btn-text">Send OTP</span>
                        <span class="spinner-border spinner-border-sm d-none" id="fpOtpLoader"></span>
                    </button>
                </div>

                <!-- Step 2: OTP -->
                <div id="fpStepOtp" class="d-none">
                    <label class="form-label small text-white">Please enter the OTP sent to your email</label>
                    <input type="text" id="fpOtpInput" class="form-control form-control-sm mb-2 text-center" placeholder="Enter OTP" style="background:#2d2d2d;color:#fff;border:none;">
                    <button class="btn btn-success w-100 btn-sm" id="fpVerifyOtpBtn">
                        Verify OTP
                    </button>
                    <button class="btn btn-link btn-sm w-100 text-white" id="fpResendOtpBtn">
                        Resend OTP
                    </button>
                </div>

                <!-- Step 3: New Password -->
                <div id="fpStepReset" class="d-none text-white w-100" style="background:#1f1f1f; padding:16px; border-radius:10px;">
                    <label class="form-label small">New Password</label>
                    <input type="password" id="fpNewPassword" class="form-control border-0 mb-3" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;">

                    <label class="form-label small">Confirm Password</label>
                    <input type="password" id="fpConfirmPassword" class="form-control border-0 mb-3" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;">

                    <button id="fpResetPasswordBtn" class="btn w-100 text-white fw-semibold shadow" style="background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%); border: 2px solid #791218; height:46px; letter-spacing:1px;">
                        Reset Password
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('scripts')
<script src="{{ asset('assets/js/rental-details.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
@endpush
