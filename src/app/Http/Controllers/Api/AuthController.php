<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * POST /api/auth/register
     * US-01: Register Account (Pembeli)
     * US-08 step-1: Register Account (Seller)
     */
    public function register(RegisterRequest $request): JsonResponse
{
    try {
        $result = $this->authService->register($request->validated());
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 422);
    }

     return response()->json([
        'success' => true,
        'message' => 'Akun berhasil dibuat.',
        'data'    => [
            'user'  => $result['user'],
            'token' => $result['token'],
        ],
    ], 201);
}

    /**
     * POST /api/auth/login
     * US-02: Login Account (Pembeli)
     * US-09: Login Account (Pemilik UMKM)
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->email,
                $request->password
            );
        } catch (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(), // pesan generik dari AuthService
            ], 401);
        }

        // Tambahkan notifikasi verifikasi untuk seller (AC US-09)
        $notice = null;
        if ($result['storeStatus'] === 'menunggu') {
            $notice = 'Toko Anda sedang dalam proses verifikasi. Beberapa fitur mungkin belum tersedia.';
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user'   => $result['user'],
                'token'  => $result['token'],
                'notice' => $notice,
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     * Hapus semua token aktif milik user yang sedang login.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * GET /api/auth/me
     * Ambil data user yang sedang login (dipakai Vue untuk cek sesi).
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user()->load('store'),
        ]);
    }
}
