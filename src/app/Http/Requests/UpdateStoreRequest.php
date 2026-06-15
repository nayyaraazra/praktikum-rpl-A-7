<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name'      => ['sometimes', 'required', 'string', 'max:255'],
            'category'        => ['sometimes', 'required', 'string'],
            'description'     => ['nullable', 'string', 'max:1000'],
            'address'         => ['sometimes', 'required', 'string'],
            'district'        => ['sometimes', 'required', 'string'],
            'operating_hours' => ['sometimes', 'required', 'string'],
            'store_category'  => ['sometimes', 'string'],
            'instagram'       => ['nullable', 'string', 'max:255'],
            'whatsapp'        => ['nullable', 'string', 'max:20'],
            'store_logo'      => ['nullable', 'image', 'max:2048'],
        ];
    }
}
