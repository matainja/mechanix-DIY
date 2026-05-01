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



    {{-- SECTION 2 --}}
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

<section id="technician-site" class="service-section">
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

</section>

    {{-- SECTION 4 --}}
    <section id="ac-service" class="service-section">
        <h2 class="section-title">AC Service</h2>

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
    <section id="alignment" class="service-section">
        <h2 class="section-title">Alignment</h2>

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

</div>
</main>

@endsection


@push('scripts')
<script src="{{ asset('assets/js/commonpage.js') }}"></script>
@endpush