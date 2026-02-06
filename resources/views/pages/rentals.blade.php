@extends('layouts.main')
@section('title', 'Rental – Mechanix D.I.Y.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/rentals.css') }}">
@endpush

@section('content')

<main class="rentals-wrap py-4 py-md-5" id="rentals">
    <div class="container">

        <div class="rentals-grid">

            @forelse($rentals as $rental)

                @php
                    $img = $rental->images->where('is_default',1)->first();
                    $price = $rental->prices->where('is_default',1)->first();
                @endphp

                <article class="rental-card">

                    {{-- Image --}}
                    <div class="rental-img">
                        <img
                            src="{{ $img ? asset('storage/'.$img->image_path) : asset('assets/images/no-image.png') }}"
                            alt="{{ $rental->name }}"
                        >
                    </div>

                    {{-- Title --}}
                    <h3 class="rental-title">
                        {{ $rental->name }}
                    </h3>

                    {{-- Optional: show price --}}
                    {{-- @if($price)
                        <p class="text-muted small mb-2">
                            ₹ {{ $price->price }} / {{ $price->hours }} hrs
                        </p>
                    @endif --}}

                    {{-- Button --}}
                    <a href="{{ route('rental.details', $rental->id) }}" class="rental-btn">
                        Book Now
                    </a>

                </article>

            @empty
                <div class="text-center w-100 py-5">
                    <p class="text-muted">No rentals available right now</p>
                </div>
            @endforelse

        </div>

    </div>
</main>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/rentals.js') }}"></script>
@endpush
