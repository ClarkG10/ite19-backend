<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    protected $fillable = ['email', 'password', 'business_name', 'business_type', 'business_number', 'business_address', 'phone_number', 'country', 'city', 'zipcode', 'operating_hours', 'image_path'];

    public function reorderRequests()
    {
        return $this->hasMany(ReorderRequest::class, 'id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'store_id');
    }

    public function orders()
    {
        return $this->hasMany(Orders::class, 'store_id');
    }

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
        'password' => 'hashed',
    ];
}
