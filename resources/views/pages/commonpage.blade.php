@extends('layouts.main')
@section('title', 'Rental – Mechanix D.I.Y.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/commonpage.css') }}">
@endpush

@section('content')

<main class="rentals-wrap py-4 py-md-5">
<div class="container">

    {{-- SECTION 1 --}}
    <section id="lift-rentals" class="service-section">
        <h2 class="section-title">Lift Rentals</h2>

        @forelse($rentals as $rental)

            @php
                $img = $rental->images->where('is_default',1)->first();
                $isAvailable = $rental->status == 1;
            @endphp

            <div class="list-card {{ !$isAvailable ? 'disabled-row' : '' }}">
                <div class="list-image">
                    <img src="{{ $img ? asset('storage/'.$img->image_path) : asset('assets/images/no-image.png') }}">
                </div>

                <div class="list-content">
                    <h3>{{ $rental->name }}</h3>
                    <p>{{$rental->description}}</p>
                </div>

                <div class="list-action">
                    <a href="{{ route('rental.details',$rental->id) }}" class="rental-btn">
                     Book Now
                    </a>
                </div>

                
            </div>

        @empty
            <p class="text-white">No rentals available</p>
        @endforelse
    </section>



    <!-- {{-- SECTION 2 --}}
    <section id="speciality-tools" class="service-section">
        <h2 class="section-title">Speciality Tools</h2>

        @forelse($rentals as $rental)

            @php
                $img = $rental->images->where('is_default',1)->first();
                $isAvailable = $rental->status == 1;
            @endphp

            <div class="list-card {{ !$isAvailable ? 'disabled-row' : '' }}">
                <div class="list-image">
                    <img src="{{ $img ? asset('storage/'.$img->image_path) : asset('assets/images/no-image.png') }}">
                </div>

                <div class="list-content">
                    <h3>{{ $rental->name }}</h3>
                    <p>Premium rental product available for booking.</p>
                </div>

                {{-- <div class="list-action">
                    <a href="{{ route('rental.details',$rental->id) }}" class="rental-btn">
                        Book Now
                    </a>
                </div> --}}
                <div class="list-action">
    <a href="tel:7327307712" class="rental-btn">
        Call Now To Book

        <span style="display:block;font-size:12px;font-weight:500;margin-top:4px;">
            732-730-7712 EXTENSION 3
        </span>
    </a>
</div>
            </div>

        @empty
        @endforelse
    </section> -->

    {{-- SECTION 2 --}}
<section id="speciality-tools" class="service-section">
    <h2 class="section-title">Speciality Tools</h2>

    {{-- Demo Item 1 --}}
    <div class="list-card">
        <div class="list-image">
            <img src="{{ asset('assets/images/rentals/ac-machine-r1234yf.png') }}" alt="AC Machine R1234yf">
        </div>
        <div class="list-content">
            {{-- <h3>Additional Equipment Options</h3> --}}
            {{-- <p>Professional A/C recovery and recharge machine for newer vehicles using R1234yf refrigerant (2017+). Fully automatic with leak detection and vacuum pump.</p> --}}
         
            <ul style="color:#000;">
    {{-- <li>
        Specialty tools can be added to any lift or rental booking upon request and availability.
    </li> --}}

   <ul class="details-features tools-grid">
    <li>A/C Machine</li>
    <li>Tire Changer & Balancer</li>
    <li>20-Ton Shop Press</li>
    <li>Engine Hoist</li>
    <li>Pipe Bender</li>
    <li>Nitrogen Tire Welder</li>
    <li>Bearing Puller</li>
    <li>O2 Sensor Socket Set</li>
    <li>Rear Caliper Tool</li>
    <li>Strut Spring Compressor</li>
    <li>Compression Tester</li>
    <li>Radiator Pressure Test Kit</li>
    <li>Disconnect Tool Set</li>
    <li>Fan Clutch Wrench Set</li>
    <li>Fuel Pressure Tester</li>
    <li>Oxy-Acetylene Torch</li>
    <li>MAP Gas Torch</li>
</ul>
</ul>
        </div>

       {{-- <div class="list-action">
    <a href="tel:7327307712" class="rental-btn">
        Call Now To Book

        <span style="display:block;font-size:12px;font-weight:500;margin-top:4px;">
            732-730-7712 EXTENSION 3
        </span>
    </a>
</div> --}}
    </div>

    {{-- Demo Item 2 --}}
    {{-- <div class="list-card">
        <div class="list-image">
            <img src="{{ asset('assets/images/rentals/ac-machine-r134a.png') }}" alt="AC Machine R134a">
        </div>
        <div class="list-content">
            <h3>AC Machine (R134a)</h3>
            <p>Heavy-duty A/C service machine for vehicles using R134a refrigerant (1994-2016). Features automatic oil injection, refrigerant recycling, and digital display.</p>
        </div>
        <div class="list-action">
    <a href="tel:7327307712" class="rental-btn">
        Call Now To Book

        <span style="display:block;font-size:12px;font-weight:500;margin-top:4px;">
            732-730-7712 EXTENSION 3
        </span>
    </a>
</div>
    </div> --}}

    {{-- Demo Item 3 --}}
    {{-- <div class="list-card">
        <div class="list-image">
            <img src ="{{ asset('assets/images/rentals/diag-scanner.png') }}" alt="Diagnostic Scanner">
        </div>
        <div class="list-content">
            <h3>Diagnostic Scanner</h3>
            <p>Professional-grade OBD2 diagnostic scanner with bi-directional controls. Reads and clears codes for engine, ABS, airbag, transmission, and more. Compatible with all 1996+ vehicles.</p>
        </div>
     <div class="list-action">
    <a href="tel:7327307712" class="rental-btn">
        Call Now To Book

        <span style="display:block;font-size:12px;font-weight:500;margin-top:4px;">
            732-730-7712 EXTENSION 3
        </span>
    </a>
</div>
    </div> --}}
{{-- <div class="speciality-note">
    <ul>
        <li>
            Specialty tools can be added to any lift or rental booking upon request and availability.
        </li>

        <li>
            Please contact our team during booking confirmation to reserve required specialty equipment in advance.
        </li>
    </ul>
</div> --}}
</section>



    {{-- SECTION 3 --}}
    <!-- <section id="technician-site" class="service-section">
        <h2 class="section-title">Technician On Site</h2>

        @forelse($rentals as $rental)

            @php
                $img = $rental->images->where('is_default',1)->first();
                $isAvailable = $rental->status == 1;
            @endphp

            <div class="list-card {{ !$isAvailable ? 'disabled-row' : '' }}">
                <div class="list-image">
                    <img src="{{ $img ? asset('storage/'.$img->image_path) : asset('assets/images/no-image.png') }}">
                </div>

                <div class="list-content">
                    <h3>{{ $rental->name }}</h3>
                    <p>Premium rental product available for booking.</p>
                </div>

                <div class="list-action">
                    <a href="{{ route('rental.details',$rental->id) }}" class="rental-btn">
                        Book Now
                    </a>
                </div>
            </div>

        @empty
        @endforelse
    </section> -->

{{-- <section id="technician-site" class="service-section">
    <h2 class="section-title">Technician On Site</h2>

    <!-- Technician 1 -->
    <div class="list-card">
        <div class="list-image">
                                            <img src="{{ asset('assets/images/icons/technician.png') }}" alt="Technician" />

        </div>
        <div class="list-content">
            <h3>Rahul Sharma</h3>
            <p>Engine Specialist • 6+ Years Experience</p>
        </div>
        <div class="list-action">
<a href="{{route('membership')}}" class="rental-btn">Request</a>
        </div>
    </div>

    <!-- Technician 2 -->
    <div class="list-card">
        <div class="list-image">
                    <img src="{{ asset('assets/images/icons/technician.png') }}" alt="Technician" />
        </div>
        <div class="list-content">
            <h3>Amit Verma</h3>
            <p>Brake & Suspension Expert • 5+ Years</p>
        </div>
        <div class="list-action">
<a href="{{route('membership')}}" class="rental-btn">Request</a>
        </div>
    </div>

    <!-- Technician 3 -->
    <div class="list-card">
        <div class="list-image">
                    <img src="{{ asset('assets/images/icons/technician.png') }}" alt="Technician" />
        </div>
        <div class="list-content">
            <h3>Sandeep Yadav</h3>
            <p>Electrical Systems • 7+ Years</p>
        </div>
        <div class="list-action">
           <a href="{{route('membership')}}" class="rental-btn">Request</a>
        </div>
    </div>

    <!-- Technician 4 -->
    <div class="list-card">
        <div class="list-image">
                                <img src="{{ asset('assets/images/icons/technician.png') }}" alt="Technician" />
        </div>
        <div class="list-content">
            <h3>Vikash Singh</h3>
            <p>Car Diagnostics • 4+ Years</p>
        </div>
        <div class="list-action">
<a href="{{route('membership')}}" class="rental-btn">Request</a>
        </div>
    </div>

    <!-- Technician 5 -->
    <div class="list-card">
        <div class="list-image">
                                <img src="{{ asset('assets/images/icons/technician.png') }}" alt="Technician" />
        </div>
        <div class="list-content">
            <h3>Arjun Das</h3>
            <p>General Service & Maintenance • 5+ Years</p>
        </div>
        <div class="list-action">
            <a href="{{route('membership')}}" class="rental-btn">Request</a>
        </div>
    </div>

</section> --}}

    <!-- {{-- SECTION 4 --}}
    <section id="Discounted-parts" class="service-section">
        <h2 class="section-title">Discounted Parts Available</h2>

        @forelse($rentals as $rental)

            @php
                $img = $rental->images->where('is_default',1)->first();
                $isAvailable = $rental->status == 1;
            @endphp

            <div class="list-card {{ !$isAvailable ? 'disabled-row' : '' }}">
                <div class="list-image">
                    <img src="{{ $img ? asset('storage/'.$img->image_path) : asset('assets/images/no-image.png') }}">
                </div>

                <div class="list-content">
                    <h3>{{ $rental->name }}</h3>
                    <p>Premium rental product available for booking.</p>
                </div>

                <div class="list-action">
                    <a href="{{ route('rental.details',$rental->id) }}" class="rental-btn">
                        Book Now
                    </a>
                </div>
            </div>

        @empty
        @endforelse
    </section>



    {{-- SECTION 5 --}}
    <section id="basic-tool" class="service-section">
        <h2 class="section-title">Basic Tools Provided</h2>

        @forelse($rentals as $rental)

            @php
                $img = $rental->images->where('is_default',1)->first();
                $isAvailable = $rental->status == 1;
            @endphp

            <div class="list-card {{ !$isAvailable ? 'disabled-row' : '' }}">
                <div class="list-image">
                    <img src="{{ $img ? asset('storage/'.$img->image_path) : asset('assets/images/no-image.png') }}">
                </div>

                <div class="list-content">
                    <h3>{{ $rental->name }}</h3>
                    <p>Premium rental product available for booking.</p>
                </div>

                <div class="list-action">
                    <a href="{{ route('rental.details',$rental->id) }}" class="rental-btn">
                        Book Now
                    </a>
                </div>
            </div>

        @empty
        @endforelse
    </section> -->

    {{-- SECTION 4 --   commented out as per new design --}}
{{-- <section id="Discounted-parts" class="service-section">
    <h2 class="section-title">Call Ahead for Discounted Parts</h2> --}}

    {{-- Demo Item 1 --}}
    {{-- <div class="list-card">
        <div class="list-image">
            <img src="https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=400&h=300&fit=crop" alt="Brake Pads">
        </div>
        <div class="list-content">
            <h3>ACDelco Professional Brake Pad Set</h3>
            <p>OEM-quality ceramic brake pads for sedans and SUVs. Perfect for NJ winter conditions. 25% off this month!</p>
        </div>
        <div class="list-action">
            <a href="#" class="rental-btn">
                Order Now
            </a>
        </div>
    </div> --}}

    {{-- Demo Item 2 --}}
    {{-- <div class="list-card">
        <div class="list-image">
            <img src="https://images.unsplash.com/photo-1625047509168-a7026f36de04?w=400&h=300&fit=crop" alt="Wiper Blades">
        </div>
        <div class="list-content">
            <h3>Bosch All-Season Wiper Blades</h3>
            <p>Rain-ready wipers essential for New Jersey weather. Fits most vehicles. Limited time - 30% discount!</p>
        </div>
        <div class="list-action">
            <a href="#" class="rental-btn">
                Order Now
            </a>
        </div>
    </div>

</section> --}}

{{-- SECTION 5 --}}
<section id="basic-tool" class="service-section">
    <h2 class="section-title">Basic Hand Tools And Cart</h2>

    {{-- Demo Item 1 --}}
    <div class="list-card">
    <div class="list-image">
        <img src="{{ asset('assets/images/rentals/shared image.jpg') }}" alt="Garage Tools">
    </div>

    <div class="list-content">
        <h3>Included Hand Tools And Equipment</h3>

        {{-- <p>
            Complete hand tool sets and professional garage equipment
            available for DIY repairs, maintenance, and upgrades.
        </p> --}}

        <div style="color:#000; line-height:1.8; font-size:14px;">
            Sockets & Impact Sockets,
            Wrenches & Ratchets,
            Extensions,
            Impact Drill,
            Electric Ratchet,
            Screwdrivers,
            Allen Keys,
            Picks,
            Pliers,
            Mini Sledge Hammer & More
        </div>

        <p style="margin-top:12px; color:#000; font-weight:600;">
            Basic hand tools and cart included in bay rental.
        </p>
    </div>
</div>

    {{-- Demo Item 2 --}}
    {{-- <div class="list-card">
        <div class="list-image">
            <img src="{{ asset('assets/images/rentals/shared image (1).jpg') }}" alt="Hydraulic Jack">
        </div>
        <div class="list-content">
            <h3>Professional Automotive Tool Set</h3>
<p>High-quality hand tools and specialty garage equipment for efficient DIY repairs, maintenance work, and vehicle upgrades.</p>
        </div> --}}
        {{-- <div class="list-action">
            <a href="#" class="rental-btn">
                Reserve Tool
            </a>
        </div> --}}
    {{-- </div>
<div class="speciality-note">
    <ul>
        <li>
            Basic hand tools and tool carts are available with bay rentals based on availability.
        </li>

        <li>
            Customers are encouraged to confirm tool availability with our team prior to or during booking confirmation.
        </li>
    </ul>
</div> --}}
</section>

</div>
</main>

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
<script src="{{ asset('assets/js/commonpage.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
@endpush