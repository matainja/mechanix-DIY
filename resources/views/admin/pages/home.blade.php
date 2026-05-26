@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Home</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                            {{-- <li class="breadcrumb-item" aria-current="page">Home</li> --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <!-- [ Main Content ] start -->
        <div class="row">

            <!-- Confirmed Bookings -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">
                            Confirmed Bookings
                        </h6>

                        <h4 class="mb-3">
                            {{ number_format($confirmedBookings) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Pending Bookings -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">
                            Pending Bookings
                        </h6>

                        <h4 class="mb-3">
                            {{ number_format($pendingBookings) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- canceled Bookings -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">
                            Cancelled Bookings
                        </h6>

                        <h4 class="mb-3">
                            {{ number_format($cancelledBookings) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Regular Bookings -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">
                            Regular Bookings
                        </h6>

                        <h4 class="mb-3">
                            {{ number_format($regularBookings) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Guest Bookings -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">
                            Guest Bookings
                        </h6>

                        <h4 class="mb-3">
                            {{ number_format($guestBookings) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">
                            Total Users
                        </h6>

                        <h4 class="mb-3">
                            {{ number_format($totalUsers) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Total Holidays -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">
                            Total Holidays
                        </h6>

                        <h4 class="mb-3">
                            {{ number_format($totalHolidays) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Overall Revenue -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">
                            Overall Revenue
                        </h6>

                        <h4 class="mb-3">
                            ${{ number_format($totalRevenue, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="f-w-400 text-muted mb-0">
                                Monthly Revenue
                            </h6>

                            <form method="GET">
                                <select name="month"
                                    class="form-select form-select-sm"
                                    onchange="this.form.submit()">

                                    <option value="">All</option>

                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}"
                                        {{ $selectedMonth == $i ? 'selected' : '' }}>
                                        {{ date('M', mktime(0, 0, 0, $i, 1)) }}
                                        </option>
                                        @endfor

                                </select>
                            </form>
                        </div>

                        <h4 class="mb-3">
                            ${{ number_format($monthlyRevenue, 2) }}
                        </h4>

                    </div>
                </div>
            </div>

        </div>

        <!-- Monthly Revenue Table -->
      <!-- Booking Details Tabs -->
<div class="card mt-4">

    <div class="card-header">

        <ul class="nav nav-tabs card-header-tabs flex-wrap" id="bookingTabs" role="tablist">

            <!-- Confirmed -->
            <li class="nav-item">
                <button class="nav-link active"
                        data-bs-toggle="tab"
                        data-bs-target="#confirmed"
                        type="button">
                    Confirmed Bookings
                </button>
            </li>

            <!-- Pending -->
            <li class="nav-item">
                <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#pending"
                        type="button">
                    Pending Bookings
                </button>
            </li>

            <!-- Cancelled -->
            <li class="nav-item">
                <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#cancelled"
                        type="button">
                    Cancelled Bookings
                </button>
            </li>

            <!-- Regular -->
            <li class="nav-item">
                <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#regular"
                        type="button">
                    Regular Users
                </button>
            </li>

            <!-- Guest -->
            <li class="nav-item">
                <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#guest"
                        type="button">
                    Guest Users
                </button>
            </li>

            <!-- Users -->
            <li class="nav-item">
                <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#users"
                        type="button">
                   Total Users
                </button>
            </li>

            <!-- Holidays -->
            <li class="nav-item">
                <button class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#holidays"
                        type="button">
                    Holidays
                </button>
            </li>

        </ul>

    </div>

    <div class="card-body">

        <div class="tab-content">

            <!-- ================= CONFIRMED ================= -->
            <div class="tab-pane fade show active" id="confirmed">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Hours</th>
                                <th>Lift Type</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($confirmedBookingList as $booking)

                                <tr>

                                    <td>{{ $booking->id }}</td>

                                    <td>
                                        {{ $booking->guest_name ?? optional($booking->user)->name }}
                                    </td>

                                    <td>
                                        {{ optional($booking->user)->email ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $booking->guest_phone ?? optional($booking->user)->phone }}
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>

                                    <td>{{ $booking->start_time }}</td>

                                    <td>{{ $booking->hours }}</td>

                                    <td>{{ ucfirst($booking->lift_type) }}</td>

                                    <td>
                                        ${{ number_format($booking->total,2) }}
                                    </td>

                                    <td>
                                        <span class="badge 
                                            @if($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning
                                            @elseif($booking->status == 'cancelled') bg-danger
                                            @else bg-secondary
                                            @endif
                                        ">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="10" class="text-center">
                                        No confirmed bookings found
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
 <div class="mt-3">{{ $confirmedBookingList->links('pagination::bootstrap-5') }}</div>
                </div>

            </div>

            <!-- ================= PENDING ================= -->
            <div class="tab-pane fade" id="pending">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Lift Type</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($pendingBookingList as $booking)

                                <tr>

                                    <td>{{ $booking->id }}</td>

                                    <td>
                                        {{ $booking->guest_name ?? optional($booking->user)->name }}
                                    </td>

                                    <td>
                                        {{ optional($booking->user)->email ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $booking->guest_phone ?? optional($booking->user)->phone }}
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>

                                    <td>{{ $booking->start_time }}</td>

                                    <td>{{ ucfirst($booking->lift_type) }}</td>

                                    <td>
                                        ${{ number_format($booking->total,2) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-warning">
                                            Pending
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="9" class="text-center">
                                        No pending bookings found
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
 <div class="mt-3">{{ $pendingBookingList->links('pagination::bootstrap-5') }}</div>
                </div>

            </div>

            <!-- ================= CANCELLED ================= -->
            <div class="tab-pane fade" id="cancelled">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Lift Type</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($cancelledBookingList as $booking)

                                <tr>

                                    <td>{{ $booking->id }}</td>

                                    <td>
                                        {{ $booking->guest_name ?? optional($booking->user)->name }}
                                    </td>

                                    <td>
                                        {{ $booking->guest_phone ?? optional($booking->user)->phone }}
                                    </td>

                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>

                                    <td>{{ $booking->start_time }}</td>

                                    <td>{{ ucfirst($booking->lift_type) }}</td>

                                    <td>
                                        ${{ number_format($booking->total,2) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-danger">
                                            Cancelled
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="text-center">
                                        No cancelled bookings found
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
 <div class="mt-3">{{ $cancelledBookingList->links('pagination::bootstrap-5') }}</div>
                </div>

            </div>

            <!-- ================= REGULAR ================= -->
            <div class="tab-pane fade" id="regular">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Lift Type</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($regularBookingList as $booking)

                                <tr>

                                    <td>{{ $booking->id }}</td>

                                    <td>{{ optional($booking->user)->name }}</td>

                                    <td>{{ optional($booking->user)->email }}</td>

                                    <td>{{ optional($booking->user)->phone }}</td>

                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>

                                    <td>{{ ucfirst($booking->lift_type) }}</td>

                                    <td>
                                        ${{ number_format($booking->total,2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7" class="text-center">
                                        No regular bookings found
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
 <div class="mt-3">{{ $regularBookingList->links('pagination::bootstrap-5') }}</div>
                </div>

            </div>

            <!-- ================= GUEST ================= -->
            <div class="tab-pane fade" id="guest">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Guest Name</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Lift Type</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($guestBookingList as $booking)

                                <tr>

                                    <td>{{ $booking->id }}</td>

                                    <td>{{ $booking->guest_name }}</td>

                                    <td>{{ $booking->guest_phone }}</td>

                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>

                                    <td>{{ $booking->start_time }}</td>

                                    <td>{{ ucfirst($booking->lift_type) }}</td>

                                    <td>
                                        ${{ number_format($booking->total,2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="7" class="text-center">
                                        No guest bookings found
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
 <div class="mt-3">{{ $guestBookingList->links('pagination::bootstrap-5') }}</div>
                </div>

            </div>

            <!-- ================= USERS ================= -->
            <div class="tab-pane fade" id="users">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Created</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($usersList as $user)

                                <tr>

                                    <td>{{ $user->id }}</td>

                                    <td>{{ $user->name }}</td>

                                    <td>{{ $user->email }}</td>

                                    <td>{{ $user->phone }}</td>

                                    <td>
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center">
                                        No users found
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
 <div class="mt-3">{{ $usersList->links('pagination::bootstrap-5') }}</div>
                </div>

            </div>

            <!-- ================= HOLIDAYS ================= -->
            <div class="tab-pane fade" id="holidays">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Holiday Name</th>
                                <th>Date</th>
                                <th>Day</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($holidaysList as $holiday)

                                <tr>

                                    <td>{{ $holiday->id }}</td>

                                    <td>{{ $holiday->holiday_name }}</td>

                                    <td>{{ $holiday->holiday_date }}</td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($holiday->date)->format('l') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center">
                                        No holidays found
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
 <div class="mt-3">{{ $holidaysList->links('pagination::bootstrap-5') }}</div>
                </div>

            </div>

        </div>

    </div>

</div>
        <!-- [ Main Content ] end -->

    </div>
</div>
@endsection