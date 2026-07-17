<h2>New Booking Received</h2>

<p><strong>Booking ID:</strong> {{ $booking->id }}</p>
<p><strong>Type:</strong> {{ $booking->booking_type === 'guest' ? 'Guest' : 'Registered User' }}</p>

@if ($booking->booking_type === 'guest')
    <p><strong>Name:</strong> {{ $booking->guest_name }}</p>
    <p><strong>Phone:</strong> {{ $booking->guest_phone }}</p>
    @if ($booking->guest_email)
        <p><strong>Email:</strong> {{ $booking->guest_email }}</p>
    @endif
@else
    <p><strong>User:</strong> {{ $booking->user->email ?? 'User #' . $booking->user_id }}</p>
@endif

<p><strong>Lift Type:</strong> {{ $booking->lift_type }}</p>
<p><strong>Date:</strong> {{ $booking->date }}</p>
<p><strong>Start Time:</strong> {{ $booking->start_time }}</p>
<p><strong>Duration:</strong> {{ $booking->hours }} hour(s)</p>
<p><strong>Workstation:</strong> {{ $booking->workstation }}</p>
<p><strong>Rate:</strong> ${{ $booking->rate_per_hour }}/hr</p>
<p><strong>Total:</strong> ${{ $booking->total }}</p>
<p><strong>Status:</strong> {{ $booking->status }}</p>