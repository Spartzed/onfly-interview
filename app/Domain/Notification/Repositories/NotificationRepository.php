<?php

namespace App\Domain\Notification\Repositories;

use App\Domain\Notification\Entities\Notification;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepository
{
    public function findByUserId(int $userId): Collection;
    
    public function findUnreadByUserId(int $userId): Collection;
    
    public function create(array $data): Notification;
    
    public function markAsRead(int $notificationId): bool;
    
    public function markAllAsRead(int $userId): bool;
    
    public function delete(int $notificationId): bool;
    
    public function getUnreadCount(int $userId): int;
} 