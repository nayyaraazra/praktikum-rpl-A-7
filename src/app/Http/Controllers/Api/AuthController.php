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
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => ['email' => [$e->getMessage()]],
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

    /**
     * PUT /api/auth/profile
     * Update user profile (name, email, phone_number, address).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id_user . ',id_user'],
            'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number,' . $user->id_user . ',id_user'],
            'address'      => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data'    => $user->fresh()->load('store'),
        ]);
    }

    /**
     * POST /api/auth/google
     * Login atau Registrasi menggunakan Google ID Token.
     */
    public function loginWithGoogle(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        $idToken = $request->id_token;

        // Dukung mock token untuk mempermudah testing di local & unit tests
        if (app()->environment('local', 'testing') && str_starts_with($idToken, 'mock_google_token_')) {
            $googleData = [
                'google_id' => str_replace('mock_google_token_', '', $idToken),
                'email'     => $request->email ?? 'mock_email@example.com',
                'name'      => $request->name ?? 'Mock User',
            ];
        } else {
            // Verifikasi ID Token asli dengan Google API
            $response = \Illuminate\Support\Facades\Http::get("https://oauth2.googleapis.com/tokeninfo", [
                'id_token' => $idToken,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google tidak valid atau kedaluwarsa.',
                ], 401);
            }

            $payload = $response->json();

            // Google ID token payload keys: 'sub' (ID), 'email', 'name'
            $googleData = [
                'google_id' => $payload['sub'],
                'email'     => $payload['email'],
                'name'      => $payload['name'] ?? $payload['email'],
            ];
        }

        $result = $this->authService->loginOrRegisterWithGoogle($googleData);

        $notice = null;
        if ($result['storeStatus'] === 'menunggu') {
            $notice = 'Toko Anda sedang dalam proses verifikasi. Beberapa fitur mungkin belum tersedia.';
        }

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil.',
            'data'    => [
                'user'   => $result['user'],
                'token'  => $result['token'],
                'notice' => $notice,
            ],
        ]);
    }
}
