<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMembership extends Model
{
   protected $fillable = [
    'user_id', 'guest_name', 'guest_email', 'guest_phone',
    'membership_plan_id', 'start_date', 'end_date', 
    'status', 'payment_method'
];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }
}