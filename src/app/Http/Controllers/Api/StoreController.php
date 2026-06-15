<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetupStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    use HasStoreAccess;

    /**
     * GET /api/stores
     * Ambil direktori toko UMKM (publik/buyer) yang sudah disetujui.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Store::where('verification_status', 'disetujui')
            ->withCount(['products' => function ($q) {
                $q->where('is_active', 1);
            }]);

        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('store_name', 'like', "%{$keyword}%")
                  ->orWhere('store_category', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->has('category') && $request->category !== 'Semua Toko') {
            $query->where('store_category', $request->category);
        }

        $stores = $query->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data'    => StoreResource::collection($stores->getCollection())->resolve(),
            'meta'    => [
                'current_page' => $stores->currentPage(),
                'last_page'    => $stores->lastPage(),
                'total'        => $stores->total(),
            ]
        ]);
    }

    /**
     * GET /api/stores/{id}
     * Ambil detail toko publik untuk buyer.
     */
    public function showPublic($id): JsonResponse
    {
        $store = Store::where('verification_status', 'disetujui')
            ->with(['products' => function($q) {
                $q->where('is_active', 1)->with('category');
            }])
            ->find($id);

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Toko tidak ditemukan atau belum disetujui.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => (new StoreResource($store))->resolve(),
        ]);
    }

    /**
     * POST /api/store/setup
     * Simpan profil toko pertama kali (onboarding).
     */
    public function setup(SetupStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

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
                'store_name'          => $validated['store_name'],
                'store_category'      => $validated['category'],
                'store_logo'          => $logoPath,
                'description'         => $validated['description'] ?? null,
                'address'             => $validated['address'],
                'operating_hours'     => $validated['operating_hours'],
                'district'            => $validated['district'],
                'verification_status' => 'menunggu',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Profil toko berhasil disimpan.',
            'data'    => (new StoreResource($store))->resolve(),
        ], 201);
    }

    /**
     * GET /api/seller/store
     * Ambil data toko seller yang login beserta produknya.
     */
    public function show(Request $request): JsonResponse
    {
        $store = $this->getStoreOrAbort($request->user());
        $store->load('products.category');

        return response()->json([
            'success' => true,
            'data'    => (new StoreResource($store))->resolve(),
        ]);
    }

    /**
     * PUT /api/seller/store
     * Perbarui profil toko seller yang login.
     */
    public function update(UpdateStoreRequest $request): JsonResponse
    {
        $store = $this->getStoreOrAbort($request->user());
        $validated = $request->validated();

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
            'data'    => (new StoreResource($store->fresh()->load('products.category')))->resolve(),
        ]);
    }
}