<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * POST /api/store/setup
     * Onboarding setup for new sellers
     */
    public function setup(Request $request): JsonResponse
    {
        $request->validate([
            'store_name'      => ['required', 'string', 'max:255'],
            'category'        => ['required', 'string'],
            'district'        => ['required', 'string'],
            'address'         => ['required', 'string'],
            'operating_hours' => ['required', 'string'],
            'description'     => ['nullable', 'string', 'max:200'],
            'logo'            => ['nullable', 'image', 'max:2048'],
        ]);

        // Cek apakah user sudah punya toko yang disetujui — jangan timpa statusnya
        $existingStore = Store::where('id_user', $request->user()->id_user)->first();
        if ($existingStore && $existingStore->verification_status === 'disetujui') {
            return response()->json([
                'success' => false,
                'message' => 'Toko Anda sudah disetujui dan tidak dapat diubah melalui halaman ini.',
            ], 422);
        }

        // updateOrCreate: jika sudah ada record untuk user ini, perbarui datanya;
        // jika belum ada, buat baru. Mencegah duplikat saat tombol diklik berkali-kali.
        Store::updateOrCreate(
            ['id_user' => $request->user()->id_user],   // kunci pencarian
            [
                'store_name'          => $request->store_name,
                'store_category'      => $request->category, // Fix: Save store_category!
                'description'         => $request->description,
                'address'             => $request->address,
                'operating_hours'     => $request->operating_hours,
                'district'            => $request->district,
                'verification_status' => 'menunggu',
            ]
        );

        return response()->json(['success' => true, 'message' => 'Profil toko berhasil disimpan.'], 201);
    }

    /**
     * POST /api/store/profile
     * Update existing seller store profile (US-11)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'store_name'      => ['required', 'string', 'max:255'],
            'store_category'  => ['required', 'string'],
            'district'        => ['required', 'string'],
            'address'         => ['required', 'string'],
            'operating_hours' => ['required', 'string'],
            'description'     => ['nullable', 'string', 'max:200'],
        ]);

        $user = $request->user();

        $store = Store::updateOrCreate(
            ['id_user' => $user->id_user],
            [
                'store_name'      => $request->store_name,
                'store_category'  => $request->store_category,
                'description'     => $request->description,
                'address'         => $request->address,
                'operating_hours' => $request->operating_hours,
                'district'        => $request->district,
            ]
        );

        // Fetch refreshed user info loaded with the updated store relation
        $refreshedUser = $user->fresh()->load('store');

        return response()->json([
            'success' => true,
            'message' => 'Profil toko berhasil diperbarui.',
            'data'    => $refreshedUser,
        ]);
    }
}