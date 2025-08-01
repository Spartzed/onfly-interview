<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Notification\Entities\Notification;
use App\Domain\Notification\Repositories\NotificationRepository;
use Illuminate\Database\Eloquent\Collection;

class EloquentNotificationRepository implements NotificationRepository
{
    public function findByUserId(int $userId): Collection
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findUnreadByUserId(int $userId): Collection
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        
        if (!$notification) {
            return false;
        }

        return $notification->update(['read_at' => now()]);
    }

    public function markAllAsRead(int $userId): bool
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function delete(int $notificationId): bool
    {
        $notification = Notification::find($notificationId);
        
        if (!$notification) {
            return false;
        }

        return $notification->delete();
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
} 