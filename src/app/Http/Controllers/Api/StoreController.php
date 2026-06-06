<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
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
                'description'         => $request->description,
                'address'             => $request->address,
                'operating_hours'     => $request->operating_hours,
                'district'            => $request->district,
                'store_category'      => $request->category,
                'verification_status' => 'menunggu',
            ]
        );

        return response()->json(['success' => true, 'message' => 'Profil toko berhasil disimpan.'], 201);
    }

    /**
     * Update store profile (Seller role).
     */
    public function update(Request $request): JsonResponse
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

        try {
            \DB::beginTransaction();

            $store = Store::where('id_user', $request->user()->id_user)->firstOrFail();

            if ($store->verification_status === 'disetujui') {
                // Hanya boleh update: description, operating_hours, address
                $store->description     = $request->description;
                $store->operating_hours = $request->operating_hours;
                $store->address         = $request->address;
            } else {
                // Belum disetujui, boleh update semua
                $store->store_name      = $request->store_name;
                $store->store_category  = $request->category;
                $store->district        = $request->district;
                $store->description     = $request->description;
                $store->operating_hours = $request->operating_hours;
                $store->address         = $request->address;
                $store->verification_status = 'menunggu';
            }

            $store->save();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil toko berhasil diperbarui.',
                'data'    => $store
            ], 200);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil toko.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}