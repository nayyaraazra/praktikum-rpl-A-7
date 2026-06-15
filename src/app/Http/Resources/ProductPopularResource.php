<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ProductPopularResource extends ProductCatalogResource
{
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data['sold_count'] = (int) $this->sold_count;
        return $data;
    }
}
