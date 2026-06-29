<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['sometimes', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'price'         => ['sometimes', 'numeric', 'min:0'],
            'stock'         => ['sometimes', 'integer', 'min:0'],
            'unit'          => ['sometimes', 'string', 'max:50'],
            'min_order'     => ['sometimes', 'integer', 'min:1'],
            'category_name' => ['sometimes', 'string', 'max:100'],
            'is_active'     => ['boolean'],
            'image'         => ['nullable', 'image', 'max:2048'],
        ];
    }
}
