<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Application\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $notifications = $this->notificationService->getUserNotifications($userId);
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function unread(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $notifications = $this->notificationService->getUserUnreadNotifications($userId);
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $success = $this->notificationService->markAsRead($id);
        
        return response()->json([
            'success' => $success
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $success = $this->notificationService->markAllAsRead($userId);
        
        return response()->json([
            'success' => $success
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $count = $this->notificationService->getUnreadCount($userId);
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
} 