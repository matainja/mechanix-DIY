<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BookingSlot extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'booking_id',
        'date',
        'time',
        'workstation',
        'lift_type',
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