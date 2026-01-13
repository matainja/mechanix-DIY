 
 @extends('layouts.main')
@section('title', 'Rental – Mechanix D.I.Y.')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/rental-details.css') }}">
@endpush
@section('content')


 <main class="details-wrap py-4 py-md-5">
    <div class="container">

      <a href="{{ route('rentals') }}" class="details-back">
        <i class="fa-solid fa-arrow-left"></i> Back to Rentals
      </a>

      <div class="details-card mt-3">
        <div class="row g-4 align-items-center">

          <div class="col-lg-6">
            <div class="details-imgbox">
              <img src="{{ $rental['image'] }}" alt="{{ $rental['title'] }}">
            </div>
          </div>

          <div class="col-lg-6">
            <h1 class="details-title">
                {{ $rental['title'] }}
            </h1>

            <p class="details-desc">
                {{ $rental['note'] }}
            </p>

            <ul class="details-features">
                @foreach($rental['features'] as $feature)
                    <li>{{ $feature }}</li>
                @endforeach
            </ul>

            <div class="details-pricebox">
              <div class="details-price">
                {{ $rental['price'] }}
              </div>
              <div class="details-note">
                {{ $item }}
              </div>
            </div>

            <a href="{{ route('booking') }}" class="details-bookbtn">
              Book Now
            </a>

          </div>

        </div>
      </div>

    </div>
</main>

  <div class="mx-demo-ribbon" aria-label="Demo Mode">
  DEMO
  <small>Work in progress</small>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/rental-details.js') }}"></script>
@endpush
