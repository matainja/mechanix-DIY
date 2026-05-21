@extends('layouts.main')

@section('title', 'Contact Us – Mechanix D.I.Y.')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/coming-soon.css') }}">
@endpush
@section('content')
    <main class="cs-wrap">
        <div class="container">
            <div class="row justify-content-center align-items-stretch g-4">

                <!-- LEFT SIDE -->
                <div class="col-lg-5">
                    <div class="cs-card h-100">

                        <div class="cs-icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>

                        <h1 class="cs-title">Contact Us</h1>

                        <div class="cs-sub">We’re here to help 🔧</div>

                        <div class="cs-note">
                            Reach out to us for any queries, support, or rental help.
                        </div>

                        <div class="contact-info mb-3">
                            <p><i class="fa-solid fa-phone me-2"></i>732-730-7712 EXTENSION 3</p>
                            <p><i class="fa-solid fa-location-dot me-2"></i>100 Midstreams Rd, Brick, NJ</p>
                        </div>

                        <div class="map-box">
                            <iframe
                                src="https://maps.google.com/maps?q=100%20Midstreams%20Rd%20Brick%20NJ&t=&z=13&ie=UTF8&iwloc=&output=embed"
                                loading="lazy">
                            </iframe>
                        </div>

                        <div class="cs-actions mt-3">
                            <a class="cs-btn" href="{{ route('home') }}">
                                <i class="fa-solid fa-house me-2"></i>Home
                            </a>

                            <a class="cs-btn primary" href="{{ route('rentals') }}">
                                Browse Rentals
                            </a>
                        </div>

                    </div>
                </div>

                <!-- RIGHT SIDE -->
                <div class="col-lg-5">

                    <div class="cs-card h-100">

                        <div class="cs-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>

                        <h1 class="cs-title">Send Message</h1>

                        <div class="cs-sub">
                            Contact us directly by email
                        </div>

                        <form action="mailto:mechanixdiynj@gmail.com" method="GET" class="mt-4">

                            <div class="mb-3">
                                <input type="text" name="subject" class="form-control cs-input" placeholder="Your Name"
                                    required>
                            </div>

                            <div class="mb-3">
                                <input type="email" name="cc" class="form-control cs-input" placeholder="Your Email"
                                    required>
                            </div>

                            <div class="mb-3">
                                <textarea name="body" rows="6" class="form-control cs-input" placeholder="Write your message..." required></textarea>
                            </div>

                            <button type="submit" class="cs-btn primary w-100">
                                <i class="fa-solid fa-paper-plane me-2"></i>
                                Send Email
                            </button>

                        </form>

                    </div>

                </div>

            </div>
        </div>
    </main>
@endsection
