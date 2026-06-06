<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';
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

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'id_store', 'id_store');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_product', 'id_product');
    }
}
