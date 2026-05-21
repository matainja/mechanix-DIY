<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
    ];
}