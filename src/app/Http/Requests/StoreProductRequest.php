<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'price'         => ['required', 'numeric', 'min:0'],
            'stock'         => ['required', 'integer', 'min:0'],
            'unit'          => ['required', 'string', 'max:50'],
            'min_order'     => ['required', 'integer', 'min:1'],
            'category_name' => ['required', 'string', 'max:100'],
            'is_active'     => ['boolean'],
            'image'         => ['nullable', 'image', 'max:2048'],
        ];
    }
}
