<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'price'
    ];
    public function links()
    {
        //return $this->belongsToMany(Link::class)->withTimestamps()->as('pivot_link')->orderByDesc(static::CREATED_AT);
        return $this->belongsToMany(Link::class)->orderByDesc(static::CREATED_AT);
    }
    public static function boot()
    {
        parent::boot();
        static::updated(function(Product $product){
            Cache::forget('products');
        });
        static::deleted(function(Product $product){
            Cache::forget('products');
        });
    }
}
