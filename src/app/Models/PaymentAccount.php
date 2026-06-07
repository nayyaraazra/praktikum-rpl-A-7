<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    protected $primaryKey = 'id_payment';

    protected $fillable = [
        'id_store',
        'bank_name',
        'account_number',
        'account_name',
        'qris_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
