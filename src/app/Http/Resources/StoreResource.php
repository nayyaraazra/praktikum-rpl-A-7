<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        
        $data['store_logo_url'] = $this->store_logo
            ? asset('storage/' . $this->store_logo)
            : null;

        if ($this->relationLoaded('products')) {
            $data['products'] = $this->products->map(function ($product) {
                $pData = $product->toArray();
                $pData['image_url'] = $product->image_product
                    ? asset('storage/' . $product->image_product)
                    : null;
                return $pData;
            })->all();
        } elseif (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $i => $p) {
                $img = $p['image_product'] ?? null;
                $data['products'][$i]['image_url'] = $img
                    ? asset('storage/' . $img)
                    : null;
            }
        }

        return $data;
    }
}
