<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipRequest extends Model
{
    protected $fillable = [
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'membership_plan_id',
        'amount_paid',
        'payment_method',
        'status',
        'admin_notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}