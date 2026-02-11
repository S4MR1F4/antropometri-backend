<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Get user notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->paginate(15);

        return $this->successResponse(
            data: [
                'notifications' => $notifications->items(),
                'pagination' => [
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                    'current_page' => $notifications->currentPage(),
                ]
            ]
        );
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->successResponse(message: 'Notifikasi ditandai sudah dibaca');
    }
}
