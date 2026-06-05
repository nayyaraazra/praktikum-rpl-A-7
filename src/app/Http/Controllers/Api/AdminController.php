<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Get admin dashboard data including metrics and list of stores.
     */
    public function getDashboard()
    {
        try {
            $totalStores = Store::count();
            $activeStores = Store::where('verification_status', 'disetujui')->count();
            $pendingStores = Store::where('verification_status', 'menunggu')->count();

            $stores = Store::with('owner')->latest()->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'metrics' => [
                        'total_stores' => $totalStores,
                        'active_stores' => $activeStores,
                        'pending_stores' => $pendingStores,
                    ],
                    'stores' => $stores
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a store (approve or reject).
     */
    public function verifyStore(Request $request, $id_store)
    {
        $request->validate([
            'status' => 'required|in:disetujui,dibatalkan'
        ]);

        try {
            DB::beginTransaction();

            $store = Store::findOrFail($id_store);
            $store->verification_status = $request->status;
            $store->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Store status updated successfully.',
                'data' => $store
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update store status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
