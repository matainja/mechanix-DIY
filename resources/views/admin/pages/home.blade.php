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
                            <li class="breadcrumb-item" aria-current="page">Home</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <!-- [ Total Bookings ] start -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Bookings</h6>
                        <h4 class="mb-3">{{ number_format($totalBookings) }} </h4>
                            
                        <!-- <span class="badge bg-light-primary border border-primary"><i
                                    class="ti ti-trending-up"></i> 59.3%</span>
                        <p class="mb-0 text-muted text-sm">You made an extra <span class="text-primary">35,000</span>
                            this year</p> -->
                    </div>
                </div>
            </div>
            <!-- [ Total Users ] start -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Users</h6>
                        <h4 class="mb-3">{{ number_format($totalUsers) }} </h4>
                            <!-- <span class="badge bg-light-success border border-success"><i
                                    class="ti ti-trending-up"></i> 70.5%</span>
                                
                        <p class="mb-0 text-muted text-sm">You made an extra <span class="text-success">8,900</span>
                            this year</p> -->
                    </div>
                </div>
            </div>
            <!-- [ Total Holidays ] start -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Holidays</h6>
                         <h4 class="mb-3">{{ number_format($totalHolidays) }} 
                         <!--   <span class="badge bg-light-warning border border-warning"><i
                                    class="ti ti-trending-down"></i> 27.4%</span></h4>
                        <p class="mb-0 text-muted text-sm">You made an extra <span class="text-warning">1,943</span>
                            this year</p> -->
                    </div>
                </div>
            </div>
            <!-- [ Total Revenue ] start -->
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-2 f-w-400 text-muted">Total Revenue</h6>
                        <h4 class="mb-3">${{ number_format($totalRevenue, 2) }} </h4>
                            <!-- <span class="badge bg-light-danger border border-danger"><i
                                    class="ti ti-trending-down"></i> 27.4%</span>
                        <p class="mb-0 text-muted text-sm">You made an extra <span class="text-danger">$20,395</span>
                            this year</p> -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection