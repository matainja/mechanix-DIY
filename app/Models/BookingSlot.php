<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSlot extends Model
{
    protected $fillable = [
        'booking_id',
        'date',
        'time',
        'workstation',
        'status',
    ];

    /**
     * Get the booking that owns this slot
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}