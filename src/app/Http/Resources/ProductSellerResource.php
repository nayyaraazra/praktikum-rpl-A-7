<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSellerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['image_url'] = $this->image_product
            ? asset('storage/' . $this->image_product)
            : null;
        return $data;
    }
}
