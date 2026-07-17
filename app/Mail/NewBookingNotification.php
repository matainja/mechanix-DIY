<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewBookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        $who = $this->booking->booking_type === 'guest'
            ? 'Guest (' . $this->booking->guest_name . ')'
            : 'Registered User';

        return $this->subject('New Booking — ' . $who . ' — #' . $this->booking->id)
                    ->view('emails.new-booking');
    }
}