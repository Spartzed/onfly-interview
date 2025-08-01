<?php

namespace App\Application\Services;

use App\Domain\Notification\Entities\Notification;
use App\Domain\Notification\Repositories\NotificationRepository;
use App\Domain\TravelOrder\Entities\TravelOrder;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function __construct(
        private NotificationRepository $notificationRepository
    ) {}

    public function getUserNotifications(int $userId): Collection
    {
        return $this->notificationRepository->findByUserId($userId);
    }

    public function getUserUnreadNotifications(int $userId): Collection
    {
        return $this->notificationRepository->findUnreadByUserId($userId);
    }

    public function createNotification(int $userId, string $type, array $data): Notification
    {
        return $this->notificationRepository->create([
            'user_id' => $userId,
            'type' => $type,
            'data' => $data,
            'read_at' => null
        ]);
    }

    public function markAsRead(int $notificationId): bool
    {
        return $this->notificationRepository->markAsRead($notificationId);
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->notificationRepository->markAllAsRead($userId);
    }

    public function getUnreadCount(int $userId): int
    {
        return $this->notificationRepository->getUnreadCount($userId);
    }

    public function createTravelOrderNotification(TravelOrder $travelOrder, string $action): Notification
    {
        $userId = $travelOrder->user_id;
        
        if (!$userId) {
            throw new \Exception('TravelOrder não possui user_id válido');
        }
        
        $notificationData = $this->buildTravelOrderNotificationData($travelOrder, $action);
        
        return $this->createNotification($userId, $notificationData['type'], $notificationData['data']);
    }

    private function buildTravelOrderNotificationData(TravelOrder $travelOrder, string $action): array
    {
        $statusLabels = [
            'created' => 'Criado',
            'approved' => 'Aprovado',
            'cancelled' => 'Cancelado',
            'rejected' => 'Negado'
        ];

        $typeMap = [
            'created' => 'info',
            'approved' => 'success',
            'cancelled' => 'error',
            'rejected' => 'error'
        ];

        $statusLabel = $statusLabels[$action] ?? $action;
        $type = $typeMap[$action] ?? 'info';

        return [
            'type' => $type,
            'data' => [
                'title' => "Pedido {$statusLabel}",
                'message' => "Seu pedido de viagem para {$travelOrder->destination} foi " . strtolower($statusLabel) . ".",
                'travel_order_id' => $travelOrder->id,
                'action' => $action
            ]
        ];
    }
} 