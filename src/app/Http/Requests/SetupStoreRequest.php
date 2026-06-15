<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetupStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_name'      => ['required', 'string', 'max:255'],
            'category'        => ['required', 'string'],
            'district'        => ['required', 'string'],
            'address'         => ['required', 'string'],
            'operating_hours' => ['required', 'string'],
            'description'     => ['nullable', 'string', 'max:200'],
            'logo'            => ['nullable', 'image', 'max:2048'],
        ];
    }
}
