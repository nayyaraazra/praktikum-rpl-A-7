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

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/logos'), $fileName);
            $logoPath = '/uploads/logos/' . $fileName;
        }

        $existingStore = Store::where('id_user', $request->user()->id_user)->first();
        if (!$logoPath && $existingStore) {
            $logoPath = $existingStore->logo;
        }

        // updateOrCreate: jika sudah ada record untuk user ini, perbarui datanya;
        // jika belum ada, buat baru. Mencegah duplikat saat tombol diklik berkali-kali.
        Store::updateOrCreate(
            ['id_user' => $request->user()->id_user],   // kunci pencarian
            [
                'store_name'          => $request->store_name,
                'store_category'      => $request->category,
                'description'         => $request->description,
                'address'             => $request->address,
                'operating_hours'     => $request->operating_hours,
                'district'            => $request->district,
                'logo'                => $logoPath,
                'verification_status' => 'menunggu',
            ]
        );

        // Update user roles to include 'seller'
        $user = $request->user();
        $roles = $user->roles ?? [];
        if (!in_array('seller', $roles)) {
            $roles[] = 'seller';
            $user->roles = $roles;
            $user->save();
        }

        return response()->json(['success' => true, 'message' => 'Profil toko berhasil disimpan.'], 201);
    }
}