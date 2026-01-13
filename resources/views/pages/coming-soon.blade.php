@extends('layouts.main')

@section('title', 'Home – Mechanix D.I.Y.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/coming-soon.css') }}">
@endpush

@section('content')
<main class="cs-wrap">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="cs-card">
            <div class="cs-icon">
              <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>

            <h1 class="cs-title">Coming Soon</h1>
            <div class="cs-sub">Currently in progress 🚧</div>

            <div class="cs-note">
              We’re working on this page right now. Please check back soon.
            </div>

            <div class="cs-actions">
              <a class="cs-btn" href="{{ route('home') }}">
                <i class="fa-solid fa-house me-2"></i>Back to Home
              </a>
              <a class="cs-btn primary" href="{{ route('rentals') }}">
                <i class="fa-solid fa-wrench me-2"></i>Browse Rentals
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

@endsection
