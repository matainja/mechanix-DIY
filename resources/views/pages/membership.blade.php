@extends('layouts.main')

@section('title', 'Membership – Mechanix D.I.Y.')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/coming-soon.css') }}">
@endpush

@section('content')
<main class="cs-wrap">
  <div class="container">
    
    <div class="text-center text-white mb-4">
      <h1 class="cs-title">Membership Plans</h1>
      <p class="cs-sub">Choose the plan that fits your needs 🔧</p>
    </div>

    <div class="row g-4 justify-content-center">

      <!-- Silver -->
      <div class="col-lg-4 col-md-6">
        <div class="cs-card pricing-card">
          <h3 class="plan-title">Silver</h3>
          <div class="price">$19<span>/month</span></div>

          <ul class="membership-list text-start">
            <li>✔ Basic tool access</li>
            <li>✔ Standard pricing</li>
            <li>✔ Email support</li>
          </ul>

          <a href="#" class="cs-btn w-100">Choose Plan</a>
        </div>
      </div>

      <!-- Gold -->
      <div class="col-lg-4 col-md-6">
        <div class="cs-card pricing-card featured">
          <h3 class="plan-title">Gold</h3>
          <div class="price">$39<span>/month</span></div>

          <ul class="membership-list text-start">
            <li>✔ Premium tool access</li>
            <li>✔ Discounted rentals</li>
            <li>✔ Priority booking</li>
            <li>✔ Priority support</li>
          </ul>

          <a href="#" class="cs-btn primary w-100">Most Popular</a>
        </div>
      </div>

      <!-- Platinum -->
      <div class="col-lg-4 col-md-6">
        <div class="cs-card pricing-card">
          <h3 class="plan-title">Platinum</h3>
          <div class="price">$59<span>/month</span></div>

          <ul class="membership-list text-start">
            <li>✔ All tools access</li>
            <li>✔ Maximum discounts</li>
            <li>✔ Instant booking</li>
            <li>✔ Dedicated manager</li>
          </ul>

          <a href="#" class="cs-btn w-100">Choose Plan</a>
        </div>
      </div>

    </div>

  </div>
</main>
@endsection