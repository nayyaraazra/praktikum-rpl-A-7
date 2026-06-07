<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $primaryKey = 'id_product';

    protected $fillable = [
        'id_store',
        'id_category',
        'name',
        'description',
        'price',
        'stock',
        'image_product',
        'unit',
        'min_order',
        'rating',
        'review_count',
        'is_active',
    ];

    protected $casts = [
        'price'  => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'id_store', 'id_store');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'id_product', 'id_product');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_product', 'id_product');
    }

    public function scopePublished($query)
    {
        return $query->where('is_active', 1)
            ->whereHas('store', fn($q) => $q->where('verification_status', 'disetujui'));
    }
}