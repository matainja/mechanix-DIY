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
        <ul class="nav nav-tabs card-header-tabs" id="bookingTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="today-tab" data-bs-toggle="tab" href="#today" role="tab">
                    Today's Bookings
                    <span class="badge bg-primary ms-1">{{ $todayBookings->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="all-tab" data-bs-toggle="tab" href="#all" role="tab">
                    All Bookings
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
        <div class="tab-content" id="bookingTabsContent">

            <!-- TODAY'S BOOKINGS TAB -->
            <div class="tab-pane fade show active" id="today" role="tabpanel">
                <small class="text-muted d-block mb-3">
                    Showing all bookings for today — {{ now()->format('d M Y') }}
                </small>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration (Hours)</th>
                                <th>Equipment Type</th>
                                <th>Rate per Hour</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($todayBookings as $index => $booking)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if ($booking->booking_type === 'guest')
                                            <strong>{{ $booking->guest_name ?? 'Guest User' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $booking->guest_phone ?? '—' }}</small>
                                        @else
                                            <strong>{{ ucfirst(strtok($booking->user?->email ?? 'User', '@')) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $booking->user?->email ?? '—' }}</small>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->addHours($booking->hours)->format('H:i') }}</td>
                                    <td>{{ $booking->hours }}</td>
                                    <td>{{ $booking->lift_type }}</td>
                                    <td>${{ number_format($booking->rate_per_hour, 2) }}</td>
                                    <td>
                                        <span class="badge 
                                            @if ($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning text-dark
                                            @else bg-danger @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($booking->total, 2) }}</td>
                                    <td>
                                        @if ($booking->status === 'pending')
                                            <button class="btn btn-success btn-sm me-1"
                                                onclick="bookingAction({{ $booking->id }}, 'approve')">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            <button class="btn btn-warning btn-sm me-1"
                                                onclick="bookingAction({{ $booking->id }}, 'cancel')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @elseif($booking->status === 'confirmed')
                                            <button class="btn btn-warning btn-sm me-1"
                                                onclick="bookingAction({{ $booking->id }}, 'cancel')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-danger btn-sm"
                                            onclick="bookingAction({{ $booking->id }}, 'delete')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="ti ti-calendar-off fs-4 d-block mb-2"></i>
                                        No bookings for today.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ALL BOOKINGS TAB -->
            <div class="tab-pane fade" id="all" role="tabpanel">
                <small class="text-muted d-block mb-3">
                    Approved bookings will be confirmed, cancelled bookings will be released, and deleted bookings will be permanently removed.
                </small>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Booking Date</th>
                                <th>Customer</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration (Hours)</th>
                                <th>Equipment Type</th>
                                <th>Rate per Hour</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>{{ $bookings->total() - ($bookings->firstItem() + $loop->index - 1) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
                                    <td>
                                        @if ($booking->booking_type === 'guest')
                                            <strong>{{ $booking->guest_name ?? 'Guest User' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $booking->guest_phone ?? '—' }}</small>
                                        @else
                                            <strong>{{ ucfirst(strtok($booking->user?->email ?? 'User', '@')) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $booking->user?->email ?? '—' }}</small>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->start_time)->addHours($booking->hours)->format('H:i') }}</td>
                                    <td>{{ $booking->hours }}</td>
                                    <td>{{ $booking->lift_type }}</td>
                                    <td>${{ number_format($booking->rate_per_hour, 2) }}</td>
                                    <td>
                                        <span class="badge 
                                            @if ($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning text-dark
                                            @else bg-danger @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>${{ number_format($booking->total, 2) }}</td>
                                    <td>
                                        @if ($booking->status === 'pending')
                                            <button class="btn btn-success btn-sm me-1"
                                                onclick="bookingAction({{ $booking->id }}, 'approve')">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            <button class="btn btn-warning btn-sm me-1"
                                                onclick="bookingAction({{ $booking->id }}, 'cancel')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @elseif($booking->status === 'confirmed')
                                            <button class="btn btn-warning btn-sm me-1"
                                                onclick="bookingAction({{ $booking->id }}, 'cancel')">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @endif
                                        <button class="btn btn-danger btn-sm"
                                            onclick="bookingAction({{ $booking->id }}, 'delete')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">{{ $bookings->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

                window.bookingAction = async function(id, action) {

                    if (action === 'delete') {
                        if (!confirm('Delete this booking permanently?')) return;
                    }

                    const config = {
                        approve: {
                            url: `/admin/bookings/${id}/approve`,
                            method: 'POST'
                        },
                        cancel: {
                            url: `/admin/bookings/${id}/cancel`,
                            method: 'POST'
                        },
                        delete: {
                            url: `/admin/bookings/${id}`,
                            method: 'DELETE'
                        },
                    };

                    const {
                        url,
                        method
                    } = config[action];

                    try {
                        const res = await fetch(url, {
                            method,
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();

                        if (!res.ok || !data.status) {
                            alert(data.message || 'Action failed.');
                            return;
                        }

                        location.reload();

                    } catch (e) {
                        alert('Network error. Please try again.');
                    }
                };
            })();
        </script>
    @endpush
@endsection
