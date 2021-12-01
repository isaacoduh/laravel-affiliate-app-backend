<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function scopeAffiliates($query)
    {
        return $query->where('is_admin', 0);
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', 1);
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->where('complete',1);
    }

    public function getRevenueAttribute()
    {
        return $this->orders->sum(fn(Order $order) => $order->affiliate_revenue);
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
