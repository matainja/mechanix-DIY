<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'hours',
        'lift_type',
        'workstation',
        'package_hours',
        'rate_per_hour',
        'total',
        'status',
    ];

    public function slots()
{
    return $this->hasMany(BookingSlot::class);
}
}
