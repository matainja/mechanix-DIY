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
                            <li class="breadcrumb-item" aria-current="page">Profile</li>
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

            {{-- LEFT: Profile + Edit --}}
            <div class="col-lg-4">

                {{-- Profile Info Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center py-4">

                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center
                                    justify-content-center mx-auto mb-3"
                             style="width:88px;height:88px;">
                            <span class="fw-bold text-primary" style="font-size:2rem;">
                                {{ strtoupper(substr(auth()->user()->email, 0, 1)) }}
                            </span>
                        </div>

                        <h6 class="mb-1 fw-semibold">{{ auth()->user()->email }}</h6>
                        <p class="text-muted small mb-3">
                            {{ auth()->user()->mobile_no ?? 'No phone set' }}
                        </p>

                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                <i class="ti ti-user me-1"></i> Registered User
                            </span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                <i class="ti ti-calendar me-1"></i>
                                Since {{ auth()->user()->created_at->format('M Y') }}
                            </span>
                        </div>

                    </div>

                    <div class="card-footer bg-transparent px-4 pb-4 border-top-0">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                                <i class="ti ti-mail text-muted"></i>
                                <span class="text-muted small">Email</span>
                                <span class="ms-auto small fw-medium text-truncate" style="max-width:160px;">
                                    {{ auth()->user()->email }}
                                </span>
                            </li>
                            <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                                <i class="ti ti-phone text-muted"></i>
                                <span class="text-muted small">Mobile</span>
                                <span class="ms-auto small fw-medium">
                                    {{ auth()->user()->mobile_no ?? '—' }}
                                </span>
                            </li>
                            <li class="d-flex align-items-center gap-2 py-2">
                                <i class="ti ti-calendar-check text-muted"></i>
                                <span class="text-muted small">Total Bookings</span>
                                <span class="ms-auto small fw-medium">
                                    {{ number_format($confirmedCount + $pendingCount + $cancelledCount) }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Edit Profile Card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="mb-0 fw-semibold">
                            <i class="ti ti-edit me-2 text-primary"></i>Edit Profile
                        </h6>
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('user.profile.update') }}">
                            @csrf
                            @method('PUT')

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Email Address</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    value="{{ old('email', auth()->user()->email) }}"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mobile --}}
                            <div class="mb-3">
                                <label class="form-label small fw-medium">Mobile Number</label>
                                <input
                                    type="text"
                                    name="mobile_no"
                                    class="form-control form-control-sm @error('mobile_no') is-invalid @enderror"
                                    value="{{ old('mobile_no', auth()->user()->mobile_no) }}"
                                    placeholder="+1 234 567 890"
                                >
                                @error('mobile_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-3">
                            <p class="small fw-semibold text-muted mb-2">
                                Change Password
                                <span class="fw-normal">(leave blank to keep current)</span>
                            </p>

                            <div class="mb-3">
                                <label class="form-label small fw-medium">Current Password</label>
                                <input
                                    type="password"
                                    name="current_password"
                                    class="form-control form-control-sm @error('current_password') is-invalid @enderror"
                                    placeholder="••••••••"
                                >
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-medium">New Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control form-control-sm @error('password') is-invalid @enderror"
                                    placeholder="••••••••"
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-medium">Confirm New Password</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control form-control-sm"
                                    placeholder="••••••••"
                                >
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="ti ti-device-floppy me-1"></i> Save Changes
                            </button>

                        </form>

                    </div>
                </div>

            </div>

            {{-- RIGHT: Booking Tabs --}}
            <div class="col-lg-8">

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