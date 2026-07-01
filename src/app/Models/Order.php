<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
     protected $primaryKey = 'id_order';
 
    protected $fillable = [
        'id_user',
        'order_date',
        'total_order',
        'status',
        'payment_method',
        'snap_token',
        'payment_status',
        'shipping_address',
        'note',
    ];
 
    protected $casts = [
        'order_date'  => 'datetime',
        'total_order' => 'decimal:2',
    ];
 
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
 
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_order', 'id_order');
    }
}
