<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderes()
    {
        return $this->hasMany(Ordere::class, 'user_id')->where('is_completed', 1);
    }
    public function products()
    {
        //return $this->belongsToMany(Product::class)->withTimestamps()->as('pivot_product')->orderByDesc(static::CREATED_AT);
        return $this->belongsToMany(Product::class)->orderByDesc(static::CREATED_AT);
    }

}
