<?php

namespace App\Application\Services;

use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Domain\TravelOrder\Repositories\TravelOrderRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Application\Services\ResponseService;
use App\Application\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TravelOrderService
{
    public function __construct(
        private TravelOrderRepositoryInterface $repository,
        private ResponseService $responseService,
        private NotificationService $notificationService
    ) {}

    public function createTravelOrder(array $data, User $user): TravelOrder
    {
        $data['order_id'] = 'ORD-' . strtoupper(Str::random(8));
        $data['user_id'] = $user->id;
        $data['status'] = TravelOrderStatus::REQUESTED;

        return $this->repository->create($data);
    }

    public function getTravelOrder(int $id): ?TravelOrder
    {
        return $this->repository->findById($id);
    }

    public function getTravelOrderByOrderId(string $orderId): ?TravelOrder
    {
        return $this->repository->findByOrderId($orderId);
    }

    public function getUserTravelOrders(User $user): Collection
    {
        return $this->repository->findByUser($user);
    }

    public function getAllTravelOrders(): Collection
    {
        return TravelOrder::with('user')->orderBy('created_at', 'desc')->get();
    }

    public function getTravelOrdersWithFilters(User $user, array $filters = []): Collection
    {
        $travelOrders = $user->isAdmin() 
            ? $this->getAllTravelOrders()
            : $this->getUserTravelOrders($user);

        // Aplicar filtros
        if (isset($filters['status'])) {
            $status = TravelOrderStatus::from($filters['status']);
            $travelOrders = $travelOrders->filter(function ($order) use ($status) {
                return $order->status === $status;
            });
        }

        if (isset($filters['destination'])) {
            $travelOrders = $travelOrders->filter(function ($order) use ($filters) {
                return stripos($order->destination, $filters['destination']) !== false;
            });
        }

        if (isset($filters['date_range'])) {
            $travelOrders = $travelOrders->filter(function ($order) use ($filters) {
                $departureDate = $order->departure_date;
                $returnDate = $order->return_date;
                $startDate = $filters['date_range']['start'] ?? null;
                $endDate = $filters['date_range']['end'] ?? null;

                if ($startDate) {
                    $startDate = Carbon::parse($startDate)->startOfDay();
                }
                if ($endDate) {
                    $endDate = Carbon::parse($endDate)->endOfDay();
                }

                if ($startDate && $departureDate->lt($startDate)) {
                    return false;
                }
                if ($endDate && $departureDate->gt($endDate)) {
                    return false;
                }

                if ($endDate && $returnDate->gt($endDate)) {
                    return false;
                }
                
                return true;
            });
        }

        return $travelOrders;
    }

    public function getTravelOrdersByStatus(TravelOrderStatus $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function getTravelOrdersByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function getTravelOrdersByDestination(string $destination): Collection
    {
        return $this->repository->findByDestination($destination);
    }

    public function updateTravelOrderStatus(TravelOrder $travelOrder, TravelOrderStatus $newStatus, User $user): bool
    {
        if (!$travelOrder->canChangeStatus($user)) {
            throw new \Exception('Você não tem permissão para alterar o status deste pedido.');
        }

        $travelOrder->updateStatus($newStatus);
        $travelOrder->refresh();

        $action = $newStatus === TravelOrderStatus::APPROVED ? 'approved' : 'rejected';
        $this->notificationService->createTravelOrderNotification($travelOrder, $action);

        return true;
    }

    public function cancelTravelOrder(TravelOrder $travelOrder, User $user): bool
    {
        if (!$travelOrder->canBeUpdatedBy($user)) {
            throw new \Exception('Você não tem permissão para cancelar este pedido.');
        }

        if ($travelOrder->status !== TravelOrderStatus::REQUESTED) {
            throw new \Exception('Apenas pedidos solicitados podem ser cancelados.');
        }

        $travelOrder->updateStatus(TravelOrderStatus::CANCELLED);

        $this->notificationService->createTravelOrderNotification($travelOrder, 'cancelled');

        return true;
    }

    // Métodos que retornam JsonResponse
    public function listTravelOrders(User $user, array $filters = []): JsonResponse
    {
        $travelOrders = $this->getTravelOrdersWithFilters($user, $filters);

        return response()->json([
            'data' => $travelOrders->values(),
            'meta' => [
                'total' => $travelOrders->count(),
                'user_role' => $user->role,
            ]
        ]);
    }

    public function createTravelOrderResponse(array $data, User $user): JsonResponse
    {
        $travelOrder = $this->createTravelOrder($data, $user);
        return response()->json(['data' => $travelOrder], 201);
    }

    public function getTravelOrderResponse(int $id, User $user): JsonResponse
    {
        $travelOrder = $this->getTravelOrder($id);

        if (!$travelOrder) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        if (!$travelOrder->canBeUpdatedBy($user)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json(['data' => $travelOrder]);
    }

    public function updateTravelOrderStatusResponse(int $id, TravelOrderStatus $newStatus, User $user): JsonResponse
    {
        $travelOrder = $this->getTravelOrder($id);

        if (!$travelOrder) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        try {
            $this->updateTravelOrderStatus($travelOrder, $newStatus, $user);
            return response()->json(['data' => $travelOrder->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function cancelTravelOrderResponse(int $id, User $user): JsonResponse
    {
        $travelOrder = $this->getTravelOrder($id);

        if (!$travelOrder) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        try {
            $this->cancelTravelOrder($travelOrder, $user);
            return response()->json(['data' => $travelOrder->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getTravelOrderByOrderIdResponse(string $orderId, User $user): JsonResponse
    {
        $travelOrder = $this->getTravelOrderByOrderId($orderId);

        if (!$travelOrder) {
            return response()->json(['message' => 'Pedido não encontrado'], 404);
        }

        if (!$travelOrder->canBeUpdatedBy($user)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json(['data' => $travelOrder]);
    }
} 