<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\BookingSlot;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ================================================================
// Cancel Expired Guest Bookings
// ================================================================
Artisan::command('bookings:cancel-expired', function () {

    $expiredBookings = Booking::where('booking_type', 'guest')
        ->where('status', 'pending')
        ->where('expires_at', '<=', Carbon::now())
        ->get();

    if ($expiredBookings->isEmpty()) {
        $this->info('No expired bookings to cancel.');
        return;
    }

    $count = 0;

    foreach ($expiredBookings as $booking) {
        // FIX: wrap each cancellation in a transaction so a partial failure
        //      doesn't leave orphaned slots or a booking stuck in 'pending'.
        DB::transaction(function () use ($booking) {
            // Delete pending slots first (they block future bookings)
            BookingSlot::where('booking_id', $booking->id)
                ->where('status', 'pending')  // only wipe pending — never touch confirmed slots
                ->delete();

            // Mark booking cancelled
            $booking->update(['status' => 'cancelled']);
        });

        $count++;
        $this->info("✓ Cancelled booking #{$booking->id} – {$booking->guest_name}");
    }

    $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    $this->info("Total cancelled: {$count} booking(s)");

})->purpose('Cancel expired guest bookings that were not confirmed by phone');

// ================================================================
// Schedule: run every minute
// ================================================================
Schedule::command('bookings:cancel-expired')->everyMinute();

/*
 * ╔══════════════════════════════════════════════════════════════════╗
 * ║  IMPORTANT – CRON SETUP (without this the scheduler never runs) ║
 * ╠══════════════════════════════════════════════════════════════════╣
 * ║                                                                  ║
 * ║  Add ONE line to your server's crontab:                          ║
 * ║                                                                  ║
 * ║  $ crontab -e                                                    ║
 * ║                                                                  ║
 * ║  * * * * * cd /path-to-your-project && php artisan schedule:run  ║
 * ║              >> /dev/null 2>&1                                   ║
 * ║                                                                  ║
 * ║  Replace /path-to-your-project with your actual project root,    ║
 * ║  e.g. /var/www/mechanix                                          ║
 * ║                                                                  ║
 * ║  To test manually right now without waiting for cron:            ║
 * ║  $ php artisan bookings:cancel-expired                           ║
 * ║                                                                  ║
 * ╚══════════════════════════════════════════════════════════════════╝
 */