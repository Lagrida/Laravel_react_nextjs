<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordere extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'link_id',
        'link_code',
        'link_ambassador_email',
        'first_name',
        'last_name',
        'email',
        'address',
        'city',
        'country',
        'zip'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function link()
    {
        return $this->belongsTo(Link::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getAdminRevenueAttribute()
    {
        return $this->orderItems->sum(fn(OrderItem $orderItem) => $orderItem->admin_revenue);
    }
    public function getAmbassadorRevenueAttribute()
    {
        return $this->orderItems->sum(fn(OrderItem $orderItem) => $orderItem->ambassador_revenue);
    }
}
