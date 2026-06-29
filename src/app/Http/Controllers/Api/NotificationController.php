<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications
     * Mengambil seluruh notifikasi milik user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $notifications = $user->notifications()
            ->forBuyer()
            ->latest('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * POST /api/notifications/mark-read
     * Tandai semua notifikasi user sebagai dibaca.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->notifications()
            ->forBuyer()
            ->update(['is_read' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca.'
        ]);
    }

    /**
     * POST /api/notifications/{id}/read
     * Tandai satu notifikasi tertentu sebagai dibaca.
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = $user->notifications()->findOrFail($id);
        $notification->update(['is_read' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai sebagai dibaca.'
        ]);
    }
}
