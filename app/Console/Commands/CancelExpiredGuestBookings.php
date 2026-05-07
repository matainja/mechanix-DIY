<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\BookingSlot;
use Carbon\Carbon;

class CancelExpiredGuestBookings extends Command
{
    protected $signature = 'bookings:cancel-expired';
    protected $description = 'Cancel expired guest bookings that were not confirmed';

    public function handle()
    {
        $expiredBookings = Booking::where('booking_type', 'guest')
            ->where('status', 'pending')
            ->where('expires_at', '<=', Carbon::now())
            ->get();

        foreach ($expiredBookings as $booking) {
            // Delete associated slots
            BookingSlot::where('booking_id', $booking->id)->delete();
            
            // Update booking status
            $booking->update(['status' => 'cancelled']);
            
            $this->info("Cancelled expired booking ID: {$booking->id}");
        }

        $this->info("Total cancelled: " . $expiredBookings->count());
    }
}