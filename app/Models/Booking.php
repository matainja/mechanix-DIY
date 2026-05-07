<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_phone',
        'date',
        'product_id',
        'start_time',
        'hours',
        'lift_type',
        'workstation',
        'package_hours',
        'rate_per_hour',
        'total',
        'status',
        'booking_type',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'date' => 'date',
    ];

    /**
     * Relationship: Booking has many slots
     */
    public function slots()
    {
        return $this->hasMany(BookingSlot::class);
    }

    /**
     * Relationship: Booking belongs to a user (nullable for guest bookings)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Booking belongs to a product (lift type)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: Only expired guest bookings
     */
    public function scopeExpiredGuest($query)
    {
        return $query->where('booking_type', 'guest')
                     ->where('status', 'pending')
                     ->where('expires_at', '<=', now());
    }

    /**
     * Scope: Only pending guest bookings (not expired)
     */
    public function scopePendingGuest($query)
    {
        return $query->where('booking_type', 'guest')
                     ->where('status', 'pending')
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope: Only confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Filter by workstation
     */
    public function scopeWorkstation($query, $workstation)
    {
        return $query->where('workstation', $workstation);
    }

    /**
     * Scope: Filter by date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Check if booking is expired
     */
    public function isExpired()
    {
        if ($this->booking_type !== 'guest' || $this->status !== 'pending') {
            return false;
        }

        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if booking is a guest booking
     */
    public function isGuest()
    {
        return $this->booking_type === 'guest';
    }

    /**
     * Get customer name (user or guest)
     */
    public function getCustomerNameAttribute()
    {
        return $this->user ? $this->user->name : $this->guest_name;
    }

    /**
     * Get customer contact (email or phone)
     */
    public function getCustomerContactAttribute()
    {
        return $this->user ? $this->user->email : $this->guest_phone;
    }

    /**
     * Get remaining time until expiry (for guest bookings)
     */
    public function getRemainingMinutesAttribute()
    {
        if (!$this->isGuest() || $this->status !== 'pending' || !$this->expires_at) {
            return null;
        }

        if ($this->expires_at->isPast()) {
            return 0;
        }

        return now()->diffInMinutes($this->expires_at);
    }

    /**
     * Cancel the booking and delete associated slots
     */
    public function cancel()
    {
        $this->slots()->delete();
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Confirm the booking (for admin use)
     */
    public function confirm()
    {
        $this->slots()->update(['status' => 'booked']);
        $this->update([
            'status' => 'confirmed',
            'expires_at' => null, // Clear expiry once confirmed
        ]);
    }
}