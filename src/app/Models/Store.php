<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $primaryKey = 'id_store';

    protected $fillable = [
        'id_user',
        'store_name',
        'store_category',
        'store_logo',
        'description',
        'address',
        'verification_status',
        'operating_hours',
        'district',
        'instagram',
        'whatsapp',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_store', 'id_store');
    }

    public function paymentAccounts(): HasMany
    {
        return $this->hasMany(PaymentAccount::class, 'id_store', 'id_store');
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'disetujui';
    }
}
