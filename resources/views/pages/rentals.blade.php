@extends('layouts.main')
@section('title', 'Rental – Mechanix D.I.Y.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/rentals.css') }}">
@endpush
@section('content')

    <main class="rentals-wrap py-4 py-md-5" id="rentals">
        <div class="container">

            <div class="rentals-grid">

                <!-- Row 1 -->
                <article class="rental-card">
                    <div class="rental-img">
                        <img src="{{ asset('assets/images/rentals/four-post.png') }}" alt="Four-Post Lift">
                    </div>
                    <h3 class="rental-title">Four-Post Lift</h3>
                    <a href="{{ route('rental.details', 'four-post') }}" class="rental-btn">
                        Book Now
                    </a>
                </article>

                <article class="rental-card">
                    <div class="rental-img">
                        <img src="{{ asset('assets/images/rentals/two-post.png') }}" alt="Two-Post Lift">
                    </div>
                    <h3 class="rental-title">Two-Post Lift</h3>
                    <a href="{{ route('rental.details', 'two-post') }}" class="rental-btn">
                        Book Now
                    </a>
                </article>

                <article class="rental-card">
                    <div class="rental-img">
                        <img src="{{ asset('assets/images/rentals/scissor.png') }}" alt="Scissor Lift">
                    </div>
                    <h3 class="rental-title">Scissor Lift</h3>
                    <a href="{{ route('rental.details', 'scissor') }}" class="rental-btn">
                        Book Now
                    </a>
                </article>

                <article class="rental-card">
                    <div class="rental-img">
                        <img src="{{ asset('assets/images/rentals/engine-hoist.png') }}" alt="Engine Hoist">
                    </div>
                    <h3 class="rental-title">Engine Hoist</h3>
                    <a href="{{ route('rental.details', 'engine-hoist') }}" class="rental-btn">
                        Book Now
                    </a>
                </article>

                <!-- Row 2 -->
                <article class="rental-card">
                    <div class="rental-img">
                        <img src="{{ asset('assets/images/rentals/diag-scanner.png') }}" alt="Diagnostic Scanner">
                    </div>
                    <h3 class="rental-title">Diagnostic Scanner</h3>
                    <a href="{{ route('rental.details', 'diag-scanner') }}" class="rental-btn">
                        Book Now
                    </a>
                </article>

                <article class="rental-card">
                    <div class="rental-img">
                        <img src="{{ asset('assets/images/rentals/ac-machine-r134a.png') }}" alt="AC Machine (R134a)">
                    </div>
                    <h3 class="rental-title">AC Machine (R134a)</h3>
                    <a href="{{ route('rental.details', 'ac-r134a') }}" class="rental-btn">Book Now</a>

                </article>

                <article class="rental-card">
                    <div class="rental-img">
                        <img src="{{ asset('assets/images/rentals/ac-machine-r1234yf.png') }}" alt="AC Machine (R1234yf)">
                    </div>
                    <h3 class="rental-title">AC Machine (R1234yf)</h3>
                    <a href="{{ route('rental.details', 'ac-r1234yf') }}" class="rental-btn">Book Now</a>

                </article>

                <article class="rental-card">
                    <div class="rental-img">
                        <img src="{{ asset('assets/images/rentals/tool-rentals.png') }}" alt="Tool Rentals">
                    </div>
                    <h3 class="rental-title">Tool Rentals</h3>
                    <a href="{{ route('rental.details', 'tool-rental') }}" class="rental-btn">Book Now</a>


                </article>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/rentals.js') }}"></script>
@endpush