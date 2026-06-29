<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_product'   => $this->id_product,
            'name'         => $this->name,
            'price'        => $this->price,
            'unit'         => $this->unit,
            'stock'        => $this->stock,
            'rating'       => $this->rating,
            'review_count' => $this->review_count,
            'image_url'    => $this->image_product
                ? asset('storage/' . $this->image_product)
                : null,
            'store'        => $this->store
                ? [
                    'id_store'        => $this->store->id_store,
                    'store_name'      => $this->store->store_name,
                    'district'        => $this->store->district,
                    'operating_hours' => $this->store->operating_hours,
                ]
                : null,
            'category'     => $this->category
                ? [
                    'name_category' => $this->category->name_category,
                ]
                : null,
        ];
    }
}
