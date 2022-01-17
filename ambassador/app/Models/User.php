<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function links()
    {
        return $this->hasMany(Link::class);
    }
    public function orderes()
    {
        return $this->hasMany(Ordere::class);
    }
    public function ScopeAmbassadors(Builder $query)
    {
        return $query->where('is_admin', 0);
    }
    public function ScopeAdmins(Builder $query)
    {
        return $query->where('is_admin', 1);
    }
    public function getRevenueAttribute()
    {
        return $this->orderes->where('is_completed', 1)->sum(fn(Ordere $ordere) => $ordere->ambassador_revenue);
    }
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
