
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Booking Received</title>
</head>
<body style="margin:0;padding:30px;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="650" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e5e5;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#0d6efd;padding:20px;text-align:center;">
                            <h2 style="margin:0;color:#ffffff;">
                                📅 New Booking Received
                            </h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px;">

                            <p style="font-size:16px;margin-top:0;">
                                A new booking has been submitted.
                            </p>

                            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;">

                                <tr style="background:#f8f9fa;">
                                    <td><strong>Booking ID</strong></td>
                                    <td>#{{ $booking->id }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Booking Type</strong></td>
                                    <td>
                                        {{ $booking->booking_type === 'guest' ? 'Guest' : 'Registered User' }}
                                    </td>
                                </tr>

                                @if($booking->booking_type === 'guest')

                                    <tr style="background:#f8f9fa;">
                                        <td><strong>Guest Name</strong></td>
                                        <td>{{ $booking->guest_name }}</td>
                                    </tr>

                                    <tr>
                                        <td><strong>Phone</strong></td>
                                        <td>{{ $booking->guest_phone }}</td>
                                    </tr>

                                    @if($booking->guest_email)
                                    <tr style="background:#f8f9fa;">
                                        <td><strong>Email</strong></td>
                                        <td>{{ $booking->guest_email }}</td>
                                    </tr>
                                    @endif

                                @else

                                    <tr style="background:#f8f9fa;">
                                        <td><strong>User</strong></td>
                                        <td>{{ $booking->user->email ?? 'User #'.$booking->user_id }}</td>
                                    </tr>

                                @endif

                                <tr>
                                    <td><strong>Lift Type</strong></td>
                                    <td>{{ ucfirst($booking->lift_type) }}</td>
                                </tr>

                                <tr style="background:#f8f9fa;">
                                    <td><strong>Date</strong></td>
                                    <td>{{ $booking->date }}</td>
                                </tr>

                                <tr>
                                    <td><strong>Start Time</strong></td>
                                    <td>{{ $booking->start_time }}</td>
                                </tr>

                                <tr style="background:#f8f9fa;">
                                    <td><strong>Duration</strong></td>
                                    <td>{{ $booking->hours }} Hour(s)</td>
                                </tr>

                                <tr>
                                    <td><strong>Workstation</strong></td>
                                    <td>{{ $booking->workstation }}</td>
                                </tr>

                                <tr style="background:#f8f9fa;">
                                    <td><strong>Rate</strong></td>
                                    <td>${{ number_format($booking->rate_per_hour,2) }}/hr</td>
                                </tr>

                                <tr>
                                    <td><strong>Total Amount</strong></td>
                                    <td>
                                        <strong style="color:#198754;font-size:18px;">
                                            ${{ number_format($booking->total,2) }}
                                        </strong>
                                    </td>
                                </tr>

                                <tr style="background:#f8f9fa;">
                                    <td><strong>Status</strong></td>
                                    <td>
                                        <span style="display:inline-block;padding:6px 12px;background:#ffc107;color:#000;border-radius:20px;font-weight:bold;">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px;text-align:center;background:#f8f9fa;font-size:13px;color:#777;">
                            This is an automated notification from your booking system.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>

