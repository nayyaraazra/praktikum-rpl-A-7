<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * US-01 (buyer) & US-08 step-1 (seller):
     * Buat akun baru, kembalikan user + token Sanctum.
     */
    public function register(array $data): array
{
    $newRole = $data['role']; // 'buyer' atau 'seller'

    // Cek apakah email sudah terdaftar
    $existingUser = User::where('email', $data['email'])->first();

    if ($existingUser) {
        // Cek apakah role yang diminta sudah ada
        if (in_array($newRole, $existingUser->roles ?? [])) {
            throw new \Exception("Akun dengan role {$newRole} untuk email ini sudah terdaftar.");
        }

        // Tambahkan role baru ke akun yang sudah ada
        $roles = $existingUser->roles ?? [];
        $roles[] = $newRole;
        $existingUser->update(['roles' => $roles]);

        $token = $existingUser->createToken('auth_token')->plainTextToken;
        return ['user' => $existingUser, 'token' => $token];
    }

    // Buat akun baru jika email belum terdaftar
    $user = User::create([
        'name'         => $data['name'],
        'email'        => $data['email'],
        'phone_number' => $data['phone_number'],
        'password'     => Hash::make($data['password']),
        'roles'        => [$newRole],
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;
    return compact('user', 'token');
}

    /**
     * US-02 (buyer) & US-09 (seller):
     * Autentikasi dengan email + password.
     * Pesan error generik — tidak mengungkapkan field mana yang salah (AC US-02).
     *
     * @throws AuthenticationException
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException(
                'Email atau password salah. Silakan coba lagi.'
            );
        }

        // Hapus token lama agar tidak menumpuk
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Untuk seller: cek status verifikasi toko (AC US-09)
        $storeStatus = null;
        if ($user->isSeller()) {
            $store       = $user->store;
            $storeStatus = $store?->verification_status;
        }

        return compact('user', 'token', 'storeStatus');
    }

    /**
     * Hapus semua token aktif milik user (logout).
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
