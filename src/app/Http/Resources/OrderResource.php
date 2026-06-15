<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_order'         => $this->id_order,
            'id_user'          => $this->id_user,
            'total_order'      => $this->total_order,
            'status'           => $this->status,
            'payment_method'   => $this->payment_method,
            'shipping_address' => $this->shipping_address,
            'note'             => $this->note,
            'order_date'       => $this->order_date ? $this->order_date->toIso8601String() : null,
            'items'            => $this->items->map(fn($item) => [
                'id_order_detail'   => $item->id_order_detail,
                'quantity'          => $item->quantity,
                'price_at_purchase' => $item->price_at_purchase,
                'product'           => $item->product ? [
                    'id_product' => $item->product->id_product,
                    'name'       => $item->product->name,
                    'unit'       => $item->product->unit,
                    'image_url'  => $item->product->image_product ? asset('storage/' . $item->product->image_product) : null,
                    'store'      => $item->product->store ? [
                        'id_store'     => $item->product->store->id_store,
                        'store_name'   => $item->product->store->store_name,
                        'phone_number' => $item->product->store->owner?->phone_number,
                    ] : null,
                ] : null,
            ]),
        ];
    }
}
