<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['product_id','hours','price','is_default'];
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = round((float) $value, 2);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

