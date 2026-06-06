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
     * Retrieve notifications for the current authenticated user (US-07)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $notifications = Notification::where('id_user', $user->id_user)
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->get();

        $mappedNotifications = $notifications->map(function ($notif) {
            $orderStatus = $notif->order->status ?? 'menunggu';
            
            // Map types & titles based on order status to match mockData.js expectations
            $type = 'order_received';
            $title = 'Pesanan Diterima';
            
            if ($orderStatus === 'diproses') {
                $type = 'order_processed';
                $title = 'Pesanan Diproses';
            } elseif ($orderStatus === 'selesai') {
                $type = 'order_completed';
                $title = 'Pesanan Selesai';
            } elseif ($orderStatus === 'dibatalkan') {
                $type = 'order_cancelled';
                $title = 'Pesanan Dibatalkan';
            }

            return [
                'id'         => $notif->id_notification,
                'order_id'   => 'ORD-' . str_pad($notif->id_order, 3, '0', STR_PAD_LEFT),
                'type'       => $type,
                'title'      => $title,
                'message'    => $notif->message,
                'is_read'    => $notif->is_read == 1,
                'created_at' => $notif->created_at ? $notif->created_at->toIso8601String() : now()->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $mappedNotifications,
        ]);
    }

    /**
     * PUT /api/notifications/{id}/read
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $notif = Notification::where('id_user', $user->id_user)
            ->where('id_notification', $id)
            ->first();

        if (!$notif) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        $notif->is_read = 1;
        $notif->save();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sebagai dibaca.',
        ]);
    }
}
