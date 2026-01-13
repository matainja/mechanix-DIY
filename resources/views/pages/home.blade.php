
@extends('layouts.main')

@section('title', 'Home – Mechanix D.I.Y.')

@section('content')



    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-overlay"></div>
      <div class="container hero-content">
        <h1 class="hero-title">Elevate Your Garage Experience</h1>
        <div class="hero-buttons">
          <a href="{{ route('booking') }}" class="btn btn-primary btn-lg btn-hero-primary"
            >Book Now</a
          >
          <a
            href="{{ route('rentals') }}"
            class="btn btn-outline-light btn-lg btn-hero-secondary"
            >View Rentals</a
          >
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




@endsection
