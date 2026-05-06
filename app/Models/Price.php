<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'product_id',
        'hours',
        'price',
        'is_default',
        'is_membership',
        'is_active'
    ];

    protected $casts = [
        'price' => 'float',
        'is_default' => 'boolean',
        'is_membership' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = number_format((float) $value, 2, '.', '');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* ===== HELPERS ===== */

    public function isMembership()
    {
        return $this->is_membership;
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getTotalAttribute()
    {
        return $this->price * $this->hours;
    }
}