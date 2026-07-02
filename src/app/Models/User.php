<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'password',
        'phone_number',
        'roles',
        'address',
    ];

    protected $hidden = [
        'password',
    ];

    protected $attributes = [
        'roles' => '["buyer"]', // default role jika tidak diisi
    ];

    protected $casts = [
        'password' => 'hashed', // Laravel 10+ auto-hash
        'roles'    => 'array',
    ];

    // Relasi 

    public function store(): HasOne
    {
        return $this->hasOne(Store::class, 'id_user', 'id_user');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_user', 'id_user');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_user', 'id_user');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'id_user', 'id_user');
    }

    //  Helper

    public function isBuyer(): bool
    {
    return in_array('buyer', $this->roles ?? []);
    }

    public function isSeller(): bool
    {
    return in_array('seller', $this->roles ?? []);
    }

    public function isAdmin(): bool
    {
    return in_array('admin', $this->roles ?? []);
    }
}
