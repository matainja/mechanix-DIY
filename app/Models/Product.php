<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name','description','status'];

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

