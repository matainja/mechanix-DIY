@extends('admin.layouts.admin')

@section('title', 'Bookings')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Bookings</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Management</a></li>
                            <li class="breadcrumb-item" aria-current="page">Bookings</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="card">
            <div class="card-header">
                <h5>All Bookings</h5>
            </div>
            <div class="card-body">
                <!-- Table for displaying bookings -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <!-- <th>User</th> -->
                            <th>Booking Date</th>
                            <th>Start Time</th>
                            <th>Status</th>
                            <th>Total</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
                                <td>
                                    <span class="badge @if($booking->status == 'completed') bg-success @elseif($booking->status == 'pending') bg-warning @else bg-danger @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td>${{ number_format($booking->total, 2) }}</td>
                                <td>
                                
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection
