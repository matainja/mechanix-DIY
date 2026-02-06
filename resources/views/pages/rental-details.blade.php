@extends('layouts.main')

@section('title', $rental->name.' – Mechanix D.I.Y.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/rental-details.css') }}">
@endpush


@section('content')

<main class="details-wrap py-4 py-md-5">
    <div class="container">

        <a href="{{ route('rentals') }}" class="details-back">
            <i class="fa-solid fa-arrow-left"></i> Back to Rentals
        </a>

        @php
            $defaultImage = $rental->images->where('is_default',1)->first();
            $defaultPrice = $rental->prices->where('is_default',1)->first();
        @endphp

        <div class="details-card mt-3">
            <div class="row g-4 align-items-center">

                {{-- IMAGE --}}
                <div class="col-lg-6">
                    <div class="details-imgbox">
                        <img
                            src="{{ $defaultImage ? asset('storage/'.$defaultImage->image_path) : asset('assets/images/no-image.png') }}"
                            alt="{{ $rental->name }}"
                        >
                    </div>
                </div>


                {{-- DETAILS --}}
                <div class="col-lg-6">

                    {{-- Title --}}
                    <h1 class="details-title">
                        {{ $rental->name }}
                    </h1>

                    {{-- Description --}}
                    <p class="details-desc">
                        {{ $rental->description ?? 'No description available.' }}
                    </p>


                    {{-- FEATURES (optional: split by comma or newline) --}}
                    @if($rental->description)
                        <ul class="details-features">
                            @foreach(explode("\n", $rental->description) as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    @endif


                    {{-- PRICE --}}
                    <div class="details-pricebox">
                        <div class="details-price">
                            @if($defaultPrice)
                                $ {{ $defaultPrice->price }} / {{ $defaultPrice->hours }} hrs
                            @else
                                Contact for price
                            @endif
                        </div>

                        <div class="details-note">
                            Product ID: {{ $rental->id }}
                        </div>
                    </div>


                    {{-- BOOK BUTTON --}}
                    <a href="{{ route('booking', ['product_id'=>$rental->id]) }}" class="details-bookbtn">
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
