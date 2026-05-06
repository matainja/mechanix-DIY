<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'category_id',
        'type',
        'slug',
    ];

    /* ================= RELATIONS ================= */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function defaultImage()
    {
        return $this->hasOne(Image::class)->where('is_default', 1);
    }

    public function defaultPrice()
    {
        return $this->hasOne(Price::class)->where('is_default', true);
    }
}