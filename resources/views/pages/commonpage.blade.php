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
        <h2 class="section-title">Lift  & Tool Rentals</h2>

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

                <div class="list-action">
                    <a href="{{ route('rental.details',$rental->id) }}" class="rental-btn">
                        Book Now
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
            <h3>AC Machine (R1234yf)</h3>
            <p>Professional A/C recovery and recharge machine for newer vehicles using R1234yf refrigerant (2017+). Fully automatic with leak detection and vacuum pump.</p>
        </div>
        <div class="list-action">
            <a href="#" class="rental-btn">
                Book Now
            </a>
        </div>
    </div>

    {{-- Demo Item 2 --}}
    <div class="list-card">
        <div class="list-image">
            <img src="{{ asset('assets/images/rentals/ac-machine-r134a.png') }}" alt="AC Machine R134a">
        </div>
        <div class="list-content">
            <h3>AC Machine (R134a)</h3>
            <p>Heavy-duty A/C service machine for vehicles using R134a refrigerant (1994-2016). Features automatic oil injection, refrigerant recycling, and digital display.</p>
        </div>
        <div class="list-action">
            <a href="#" class="rental-btn">
                Book Now
            </a>
        </div>
    </div>

    {{-- Demo Item 3 --}}
    <div class="list-card">
        <div class="list-image">
            <img src ="{{ asset('assets/images/rentals/diag-scanner.png') }}" alt="Diagnostic Scanner">
        </div>
        <div class="list-content">
            <h3>Diagnostic Scanner</h3>
            <p>Professional-grade OBD2 diagnostic scanner with bi-directional controls. Reads and clears codes for engine, ABS, airbag, transmission, and more. Compatible with all 1996+ vehicles.</p>
        </div>
        <div class="list-action">
            <a href="#" class="rental-btn">
                Book Now
            </a>
        </div>
    </div>

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
            <img src="{{ asset('assets/images/rentals/shared image.jpg') }}" alt="Socket Set">
        </div>
        <div class="list-content">
            <h3>Craftsman 230-Piece Mechanics Tool Set</h3>
            <p>Complete socket and wrench set for all your DIY car repairs. Includes metric and SAE sizes for foreign and domestic vehicles.</p>
        </div>
        <div class="list-action">
            <a href="#" class="rental-btn">
                Reserve Tool
            </a>
        </div>
    </div>

    {{-- Demo Item 2 --}}
    <div class="list-card">
        <div class="list-image">
            <img src="{{ asset('assets/images/rentals/shared image (1).jpg') }}" alt="Hydraulic Jack">
        </div>
        <div class="list-content">
            <h3>Professional Automotive Tool Set</h3>
<p>High-quality hand tools and specialty garage equipment for efficient DIY repairs, maintenance work, and vehicle upgrades.</p>
        </div>
        <div class="list-action">
            <a href="#" class="rental-btn">
                Reserve Tool
            </a>
        </div>
    </div>

</section>

</div>
</main>

@endsection


@push('scripts')
<script src="{{ asset('assets/js/commonpage.js') }}"></script>
@endpush