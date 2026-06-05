<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email'],
            'phone_number' => ['required', 'string', 'max:20'],
            'password'     => ['required', 'confirmed', Password::min(8)],
            'role'         => ['required', 'in:buyer,seller'],
        ];
    }

    /**
     * Pesan error sesuai AC US-01 dan US-08.
     */
    public function messages(): array
    {
        return [
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah terdaftar.',
            'phone_number.unique' => 'Nomor telepon sudah terdaftar.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'password.min'        => 'Password minimal 8 karakter.',
        ];
    }
}
