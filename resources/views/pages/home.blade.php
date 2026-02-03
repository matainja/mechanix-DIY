@extends('layouts.main')

@section('title', 'Home – Mechanix D.I.Y.')

@section('content')



    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1 class="hero-title">Elevate Your Garage Experience</h1>
            <div class="hero-buttons">
                <a href="{{ route('booking') }}" class="btn btn-primary btn-lg btn-hero-primary">Book Now</a>
                <a href="{{ route('rentals') }}" class="btn btn-outline-light btn-lg btn-hero-secondary">View Rentals</a>
            </div>
        </div>
        <!-- <div class="car-box">
                    <img src="{{ asset('assets/images/lift-car.png') }}" alt="car" width="50%" />
                  </div> -->
    </section>

    <!-- Services Icons -->
    <section class="services-icons">
        <div class="container">
            <div class="services-row">
                <a class="service-item-custom service-link" href="{{ route('coming') }}">
                    <img src="{{ asset('assets/images/icons/lift-tool-rental.png') }}" alt="Lift Rentals" />
                    <span>Lift Rentals & Tool Rentals</span>
                </a>

                <span class="service-divider">|</span>

                <a class="service-item-custom service-link" href="{{ route('coming') }}">
                    <img src="{{ asset('assets/images/tool-rental.png') }}" alt="Tool Rentals" />
                    <span>Speciality Tools</span>
                </a>

                <span class="service-divider">|</span>

                <a class="service-item-custom service-link" href="{{ route('coming') }}">
                    <img src="{{ asset('assets/images/icons/technician.png') }}" alt="Technician" />
                    <span>Technician On Site</span>
                </a>

                <span class="service-divider">|</span>

                <a class="service-item-custom service-link" href="{{ route('coming') }}">
                    <img src="{{ asset('assets/images/ac-2.png') }}" alt="AC Service" />
                    <span>AC Service</span>
                </a>

                <span class="service-divider">|</span>

                <a class="service-item-custom service-link" href="{{ route('coming') }}">
                    <img src="{{ asset('assets/images/icons/alignment.png') }}" alt="Alignment" />
                    <span>Alignment</span>
                </a>
            </div>

        </div>
    </section>

    <!-- VIP Membership -->
    <section class="membership-section">
        <div class="container text-center">
            <h2 class="membership-title">Join Our VIP Membership</h2>
            <p class="membership-subtitle">Exclusive Benefits & Discounts</p>
            <a href="{{ route('coming') }}" class="btn btn-primary btn-lg btn-member">
                <span>Become a Member</span>
            </a>
        </div>
    </section>
    <!-- Routes for JS -->
    <div id="mx-routes" data-login-url="{{ route('popup.login') }}" data-register-url="{{ route('popup.register') }}">
    </div>
    {{-- Auth Modal --}}
    <div class="modal fade" id="mxAuthModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1f1f1f; color:#fff; border-radius:10px;">

                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Continue to Book</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-white bg-dark" data-bs-toggle="tab"
                                data-bs-target="#mxTabLogin" type="button" role="tab"
                                style="border:none; border-radius:5px 5px 0 0;">
                                Login
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-white bg-dark" data-bs-toggle="tab" data-bs-target="#mxTabRegister"
                                type="button" role="tab" style="border:none; border-radius:5px 5px 0 0;">
                                Register
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">

                        {{-- LOGIN --}}
                        <div class="tab-pane fade show active" id="mxTabLogin" role="tabpanel">
                            <div id="mxLoginErr" class="alert alert-danger d-none"></div>

                            <form id="mxLoginForm">

                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-white">Email</label>
                                    <input type="email" class="form-control border-0" name="email"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-white">Password</label>
                                    <input type="password" class="form-control border-0" name="password"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                        required>
                                </div>

                                <div class="text-end mt-2">
                                    <a href="#" id="mxForgotBtn" class="small text-danger">
                                        Forgot password?
                                    </a>
                                </div>

                                <button class="btn w-100 text-white fw-semibold mt-3"
                                    style="background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%);
                                           border:2px solid #791218; height:46px; letter-spacing:1px;">
                                    Login
                                </button>

                            </form>
                        </div>

                        {{-- REGISTER --}}
                        <div class="tab-pane fade" id="mxTabRegister" role="tabpanel">
                            <div id="mxRegErr" class="alert alert-danger d-none"></div>

                            <form id="mxRegisterForm">
                                @csrf
                                <div class="row g-2">

                                    <div class="col-6">
                                        <label class="form-label small text-white">Email</label>
                                        <input type="email" class="form-control border-0 form-control-sm"
                                            name="email"
                                            style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                            required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Mobile</label>
                                        <input type="text" class="form-control border-0 form-control-sm"
                                            name="mobile_no"
                                            style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                            required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Password</label>
                                        <input type="password" class="form-control border-0 form-control-sm"
                                            name="password"
                                            style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                            required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Confirm</label>
                                        <input type="password" class="form-control border-0 form-control-sm"
                                            name="password_confirmation"
                                            style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                            required>
                                    </div>

                                </div>

                                <button class="btn w-100 text-white fw-semibold mt-3"
                                    style="background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%);
                                           border:2px solid #791218; height:46px; letter-spacing:1px;">
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
    <div class="modal fade " id="mxForgotModal" tabindex="-1">
        <div class="modal-dialog modal-sm"
            style="background: linear-gradient(180deg,#1f1f1f,#2a2a2a); border-radius:10px;">
            <div class="modal-content mt-4 p-4" style="background: transparent; border:none;">

                <!-- Step 1 Email -->
                <div id="stepEmail">
                    <h6 class="mb-3">Reset Password</h6>

                    <input type="email" id="fpEmail" class="form-control form-control-sm mb-2"
                        placeholder="Enter email">

                    <button class="btn btn-primary w-100 btn-sm" id="sendOtpBtn">
                        Send OTP
                    </button>
                </div>


                <!-- Step 2 OTP -->
                <div id="stepOtp" class="d-none">
                    <input type="text" id="fpOtp" class="form-control form-control-sm mb-2 text-center"
                        placeholder="Enter OTP">

                    <button class="btn btn-success w-100 btn-sm" id="verifyOtpBtn">
                        Verify OTP
                    </button>

                    <button class="btn btn-link btn-sm w-100" id="resendOtpBtn">
                        Resend OTP
                    </button>
                </div>


                <!-- Step 3 New Password -->
                <div id="stepReset" class="d-none text-white w-100"
                    style="background:#1f1f1f; padding:16px; border-radius:10px;">

                    <label class="form-label small">New Password</label>
                    <input type="password" id="fpPass" class="form-control border-0 mb-3"
                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;">

                    <label class="form-label small">Confirm Password</label>
                    <input type="password" id="fpPass2" class="form-control border-0 mb-3"
                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;">

                    <button id="resetPassBtn" class="btn w-100 text-white fw-semibold shadow"
                        style="
            background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%);
            border: 2px solid #791218;
            height:46px;
            letter-spacing:1px;">
                        Reset Password
                    </button>

                </div>






            </div>
        </div>
    </div>



@endsection
