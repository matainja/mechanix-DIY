@extends('admin.layouts.admin')

@section('title', 'My Profile')

@section('content')
<div class="pc-container">
    <div class="pc-content">

        {{-- Breadcrumb --}}
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">My Profile</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Session Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ===== STATS ROW ===== --}}
        <div class="row g-3 mb-4">

            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="ti ti-circle-check text-success fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Confirmed</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($confirmedCount) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="ti ti-clock text-warning fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Pending</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($pendingCount) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                            <i class="ti ti-circle-x text-danger fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Cancelled</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($cancelledCount) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="ti ti-currency-dollar text-primary fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 small">Total Spent</p>
                            <h4 class="mb-0 fw-bold">${{ number_format($totalSpent, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== MAIN LAYOUT ===== --}}
        <div class="row g-4">

           
           

            {{-- RIGHT: Booking Tabs --}}
            <div class="card-body table-responsive">

                <div class="card border-0 shadow-sm" style="overflow:visible;">
                    <div class="card-header bg-transparent border-bottom" style="padding: 0 0 0 0;">
                        <ul class="nav nav-tabs card-header-tabs mb-0"
                            id="userBookingTabs"
                            style="display:flex; flex-wrap:wrap; gap:0; border-bottom:0;">

                            <li class="nav-item" style="white-space:nowrap;">
                                <button class="nav-link active px-3 py-3 d-flex align-items-center gap-2"
                                        data-bs-toggle="tab" data-bs-target="#u-confirmed" type="button">
                                    <span class="badge bg-success rounded-pill">{{ $confirmedCount }}</span>
                                    Confirmed
                                </button>
                            </li>
                            <li class="nav-item" style="white-space:nowrap;">
                                <button class="nav-link px-3 py-3 d-flex align-items-center gap-2"
                                        data-bs-toggle="tab" data-bs-target="#u-pending" type="button">
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $pendingCount }}</span>
                                    Pending
                                </button>
                            </li>
                            <li class="nav-item" style="white-space:nowrap;">
                                <button class="nav-link px-3 py-3 d-flex align-items-center gap-2"
                                        data-bs-toggle="tab" data-bs-target="#u-cancelled" type="button">
                                    <span class="badge bg-danger rounded-pill">{{ $cancelledCount }}</span>
                                    Cancelled
                                </button>
                            </li>
                            <li class="nav-item" style="white-space:nowrap;">
                                <button class="nav-link px-3 py-3 d-flex align-items-center gap-2"
                                        data-bs-toggle="tab" data-bs-target="#u-all" type="button">
                                    <span class="badge bg-secondary rounded-pill">
                                        {{ $confirmedCount + $pendingCount + $cancelledCount }}
                                    </span>
                                    All
                                </button>
                            </li>

                        </ul>
                    </div>

                    <div class="card-body p-0">
                        <div class="tab-content">

                            @php
                                $tabs = [
                                    'u-confirmed' => ['bookings' => $confirmedBookings, 'badge' => 'bg-success',            'label' => 'Confirmed'],
                                    'u-pending'   => ['bookings' => $pendingBookings,   'badge' => 'bg-warning text-dark',  'label' => 'Pending'],
                                    'u-cancelled' => ['bookings' => $cancelledBookings, 'badge' => 'bg-danger',             'label' => 'Cancelled'],
                                    'u-all'       => ['bookings' => $allBookings,        'badge' => 'bg-secondary',         'label' => ''],
                                ];
                            @endphp

                            @foreach($tabs as $tabId => $tab)
                                <div class="tab-pane fade {{ $tabId === 'u-confirmed' ? 'show active' : '' }}"
                                     id="{{ $tabId }}">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-4">#</th>
                                                    <th>Date</th>
                                                    <th>Time</th>
                                                    <th>Hours</th>
                                                    <th>Lift Type</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($tab['bookings'] as $booking)
                                                    <tr>
                                                        <td class="ps-4 text-muted small">{{ $booking->id }}</td>
                                                        <td class="fw-medium">
                                                            {{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}
                                                        </td>
                                                        <td class="text-muted small">{{ $booking->start_time }}</td>
                                                        <td class="text-muted small">{{ $booking->hours }}h</td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border">
                                                                {{ ucfirst($booking->lift_type) }}
                                                            </span>
                                                        </td>
                                                        <td class="fw-semibold">
                                                            ${{ number_format($booking->total, 2) }}
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $tab['label'] ? $tab['badge'] : (
                                                                $booking->status === 'confirmed' ? 'bg-success' :
                                                                ($booking->status === 'pending'  ? 'bg-warning text-dark' : 'bg-danger')
                                                            ) }}">
                                                                {{ ucfirst($booking->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center py-5 text-muted">
                                                            <i class="ti ti-calendar-off d-block mb-2 fs-3"></i>
                                                            No bookings found
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($tab['bookings']->hasPages())
                                        <div class="px-4 py-3 border-top">
                                            {{ $tab['bookings']->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection