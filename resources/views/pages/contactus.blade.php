@extends('layouts.main')

@section('title', 'Contact Us – Mechanix D.I.Y.')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/coming-soon.css') }}">
@endpush
@section('content')
<main class="cs-wrap">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7 col-md-9">
        <div class="cs-card">

          <div class="cs-icon">
            <i class="fa-solid fa-phone"></i>
          </div>

          <h1 class="cs-title">Contact Us</h1>
          <div class="cs-sub">We’re here to help 🔧</div>

          <div class="cs-note">
            Reach out to us for any queries, support, or rental help.
          </div>

          <div class="contact-info mb-3">
            <p><i class="fa-solid fa-phone me-2"></i>733-730-7712</p>
            <p><i class="fa-solid fa-location-dot me-2"></i>100 Midstreams Rd, Brick, NJ</p>
          </div>

          <!-- Map -->
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
    </div>
  </div>
</main>
@endsection