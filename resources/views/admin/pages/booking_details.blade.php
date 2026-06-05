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
        @forelse ($list as $index => $booking)
            <tr>
                <td>{{ $index + 1 }}</td>
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
        @empty
            <tr>
                <td colspan="11" class="text-center text-muted py-4">
                    <i class="ti ti-calendar-off fs-4 d-block mb-2"></i>
                    No bookings found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

