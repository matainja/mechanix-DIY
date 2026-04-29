@extends('layouts.main')

@section('title', 'Privacy Policy – Mechanix D.I.Y.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/coming-soon.css') }}">
@endpush

@section('content')
<main class="cs-wrap">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10">

        <div class="cs-card text-start">

          <h1 class="cs-title text-center mb-3">Privacy Policy</h1>
          <p class="cs-note text-center mb-4">
            Your privacy is important to us. This policy explains how we handle your data.
          </p>

          <div class="policy-section">
            <h5>1. Information We Collect</h5>
            <p>
              We may collect personal information such as your name, phone number,
              email address, and usage data when you use our services.
            </p>
          </div>

          <div class="policy-section">
            <h5>2. How We Use Your Information</h5>
            <p>
              We use your information to provide services, improve user experience,
              process rentals, and communicate updates or support.
            </p>
          </div>

          <div class="policy-section">
            <h5>3. Sharing of Information</h5>
            <p>
              We do not sell or rent your personal data. Information may be shared
              with trusted partners only to operate our services effectively.
            </p>
          </div>

          <div class="policy-section">
            <h5>4. Data Security</h5>
            <p>
              We implement appropriate security measures to protect your data
              from unauthorized access or disclosure.
            </p>
          </div>

          <div class="policy-section">
            <h5>5. Cookies</h5>
            <p>
              Our website may use cookies to enhance user experience and analyze traffic.
            </p>
          </div>

          <div class="policy-section">
            <h5>6. Your Rights</h5>
            <p>
              You have the right to access, update, or delete your personal information
              by contacting us.
            </p>
          </div>

          <div class="policy-section">
            <h5>7. Contact Us</h5>
            <p>
              If you have any questions about this Privacy Policy, please contact us at:
              <br>
              <strong>Phone:</strong> 733-730-7712 <br>
              <strong>Address:</strong> 100 Midstreams Rd, Brick, NJ
            </p>
          </div>

          <div class="cs-actions mt-4 justify-content-center">
            <a class="cs-btn" href="{{ route('home') }}">
              <i class="fa-solid fa-house me-2"></i>Home
            </a>
            <a class="cs-btn primary" href="{{ route('contact') }}">
              Contact Us
            </a>
          </div>

        </div>

      </div>
    </div>
  </div>
</main>
@endsection