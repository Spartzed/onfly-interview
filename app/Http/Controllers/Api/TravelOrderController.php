<?php

namespace App\Http\Controllers\Api;

use App\Application\Services\TravelOrderService;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Http\Controllers\BaseController;
use App\Http\Requests\TravelOrder\TravelOrderStoreRequest;
use App\Http\Requests\TravelOrder\TravelOrderUpdateStatusRequest;
use App\Application\Services\AuthService;
use App\Application\Services\ExceptionHandlerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TravelOrderController extends BaseController
{
    protected AuthService $authService;

    public function __construct(
        ExceptionHandlerService $exceptionHandler,
        private TravelOrderService $travelOrderService,
        AuthService $authService
    ) {
        parent::__construct($exceptionHandler);
        $this->authService = $authService;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->handleRequest(function () use ($request) { 
            $user = $this->authService->getCurrentUser();
            $filters = $request->only(['status', 'destination', 'date_range']);
            
            return $this->travelOrderService->listTravelOrders($user, $filters);
        });
    }

    public function store(TravelOrderStoreRequest $request): JsonResponse
    {
        return $this->handleRequest(function () use ($request) {
            $user = $this->authService->getCurrentUser();
            $data = $request->only(['requester_name', 'destination', 'departure_date', 'return_date']);
            
            return $this->travelOrderService->createTravelOrderResponse($data, $user);
        });
    }

    public function show(int $id): JsonResponse
    {
        return $this->handleRequest(function () use ($id) {
            $user = $this->authService->getCurrentUser();
            return $this->travelOrderService->getTravelOrderResponse($id, $user);
        }); 
    }

    public function updateStatus(TravelOrderUpdateStatusRequest $request, int $id): JsonResponse
    {
        return $this->handleRequest(function () use ($request, $id) {
            $user = $this->authService->getCurrentUser();
            $newStatus = TravelOrderStatus::from($request->status);
            
            return $this->travelOrderService->updateTravelOrderStatusResponse($id, $newStatus, $user);
        });
    }

    public function cancel(int $id): JsonResponse
    {
        return $this->handleRequest(function () use ($id) {
            $user = $this->authService->getCurrentUser();
            return $this->travelOrderService->cancelTravelOrderResponse($id, $user);
        });
    }

    public function getByOrderId(string $orderId): JsonResponse
    {
        return $this->handleRequest(function () use ($orderId) {
            $user = $this->authService->getCurrentUser();
            return $this->travelOrderService->getTravelOrderByOrderIdResponse($orderId, $user);
        });
    }
} 