<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $store = $this->store;

        return [
            'id_product'   => $this->id_product,
            'name'         => $this->name,
            'description'  => $this->description,
            'price'        => $this->price,
            'stock'        => $this->stock,
            'unit'         => $this->unit,
            'min_order'    => $this->min_order,
            'rating'       => $this->rating,
            'review_count' => $this->review_count,
            'image_url'    => $this->image_product
                ? asset('storage/' . $this->image_product)
                : null,
            'category'     => $this->category
                ? ['name_category' => $this->category->name_category]
                : null,
            'store'        => $store
                ? [
                    'id_store'        => $store->id_store,
                    'store_name'      => $store->store_name,
                    'logo'            => $store->store_logo ? asset('storage/' . $store->store_logo) : null,
                    'description'     => $store->description,
                    'address'         => $store->address,
                    'district'        => $store->district,
                    'operating_hours' => $store->operating_hours,
                    'phone_number'    => $store->owner?->phone_number,
                    'payment_accounts' => $store->paymentAccounts->map(fn($pa) => [
                        'bank_name'      => $pa->bank_name,
                        'account_number' => $pa->account_number,
                        'account_name'   => $pa->account_name,
                        'qris_code'      => $pa->qris_code,
                    ]),
                ]
                : null,
            'reviews'      => $this->reviews->map(fn($r) => [
                'rating'     => $r->rating,
                'comment'    => $r->comment,
                'user'       => $r->user ? ['name' => $r->user->name] : null,
                'created_at' => $r->created_at,
            ]),
        ];
    }
}