<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id_product' => $this->id_product,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'price_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'stock' => (int) $this->stock,
            'unit' => $this->unit,
            'min_order' => (int) $this->min_order,
            'rating' => (float) $this->rating,
            'review_count' => (int) $this->review_count,
            'image_product' => $this->image_product,
            'is_active' => (int) $this->is_active,
            'store' => $this->relationLoaded('store') && $this->store ? [
                'id' => $this->store->id_store,
                'name' => $this->store->store_name,
                'district' => $this->store->district,
                'description' => $this->store->description,
                'address' => $this->store->address,
                'operating_hours' => $this->store->operating_hours,
            ] : null,
            'category' => $this->relationLoaded('category') && $this->category ? [
                'id' => $this->category->id_category,
                'name' => $this->category->name_category,
            ] : null,
        ];
    }
}
