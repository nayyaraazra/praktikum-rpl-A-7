<?php

namespace App\Http\Controllers\Api;

use App\Models\Store;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HasStoreAccess
{
    /**
     * Get the store of the user, or abort with a 404 response.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\Store
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function getStoreOrAbort($user): Store
    {
        $store = $user->store;

        if (!$store) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Toko belum terdaftar.',
                ], 404)
            );
        }

        return $store;
    }
}
