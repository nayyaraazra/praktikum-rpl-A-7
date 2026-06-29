<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * POST /api/store/setup
     * Simpan profil toko pertama kali (onboarding).
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

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('stores', 'public');
        }

        $store = Store::updateOrCreate(
            ['id_user' => $request->user()->id_user],
            [
                'store_name'          => $request->store_name,
                'store_category'      => $request->category,
                'store_logo'          => $logoPath,
                'description'         => $request->description,
                'address'             => $request->address,
                'operating_hours'     => $request->operating_hours,
                'district'            => $request->district,
                'verification_status' => 'menunggu',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Profil toko berhasil disimpan.',
            'data'    => $this->formatStore($store),
        ], 201);
    }

    /**
     * GET /api/seller/store
     * Ambil data toko seller yang login beserta produknya.
     */
    public function show(Request $request): JsonResponse
    {
        $store = $request->user()->store()->with('products.category')->first();

        if (! $store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko belum terdaftar.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatStore($store),
        ]);
    }

    /**
     * PUT /api/seller/store
     * Perbarui profil toko seller yang login.
     */
    public function update(Request $request): JsonResponse
    {
        $store = $request->user()->store;

        if (! $store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko belum terdaftar.',
            ], 404);
        }

        $validated = $request->validate([
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
        ]);

        if (isset($validated['category'])) {
            $validated['store_category'] = $validated['category'];
            unset($validated['category']);
        }

        if ($request->hasFile('store_logo')) {
            $validated['store_logo'] = $request->file('store_logo')->store('stores', 'public');
        }

        $store->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil toko berhasil diperbarui.',
            'data'    => $this->formatStore($store->fresh()->load('products.category')),
        ]);
    }

    /**
     * Helper: tambahkan URL gambar ke data store.
     */
    private function formatStore(Store $store): array
    {
        $data = $store->toArray();
        $data['store_logo_url'] = $store->store_logo
            ? asset('storage/' . $store->store_logo)
            : null;
        if (isset($data['products']) && is_array($data['products'])) {
            foreach ($data['products'] as $i => $p) {
                $img = $p['image_product'] ?? null;
                $data['products'][$i]['image_url'] = $img
                    ? asset('storage/' . $img)
                    : null;
            }
        }
        return $data;
    }
}