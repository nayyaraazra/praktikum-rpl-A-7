<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_product'       => ['required', 'exists:products,id_product'],
            'quantity'         => ['required', 'integer', 'min:1'],
            'name'             => ['required', 'string', 'max:255'],
            'phone_number'     => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string'],
            'payment_method'   => ['required', 'in:cod,transfer,midtrans'],
            'note'             => ['nullable', 'string', 'max:1000'],
        ];
    }
}
