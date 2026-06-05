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

        Store::create([
            'id_user'             => $request->user()->id_user,
            'store_name'          => $request->store_name,
            'description'         => $request->description,
            'address'             => $request->address,
            'operating_hours'     => $request->operating_hours,
            'district'            => $request->district,
            'verification_status' => 'menunggu',
        ]);

        return response()->json(['success' => true, 'message' => 'Profil toko berhasil disimpan.'], 201);
    }
}