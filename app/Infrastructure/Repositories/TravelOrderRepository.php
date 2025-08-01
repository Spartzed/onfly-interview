<?php

namespace App\Infrastructure\Repositories;

use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Domain\TravelOrder\Repositories\TravelOrderRepositoryInterface;
use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;

class TravelOrderRepository implements TravelOrderRepositoryInterface
{
    public function create(array $data): TravelOrder
    {
        return TravelOrder::create($data);
    }
    
    public function findById(int $id): ?TravelOrder
    {
        return TravelOrder::find($id);
    }
    
    public function findByOrderId(string $orderId): ?TravelOrder
    {
        return TravelOrder::where('order_id', $orderId)->first();
    }
    
    public function findByUser(User $user): Collection
    {
        return $user->travelOrders()->orderBy('created_at', 'desc')->get();
    }
    
    public function findByStatus(TravelOrderStatus $status): Collection
    {
        return TravelOrder::where('status', $status)->get();
    }
    
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return TravelOrder::whereBetween('departure_date', [$startDate, $endDate])
            ->orWhereBetween('return_date', [$startDate, $endDate])
            ->get();
    }
    
    public function findByDestination(string $destination): Collection
    {
        return TravelOrder::where('destination', 'like', "%{$destination}%")->get();
    }
    
    public function update(TravelOrder $travelOrder, array $data): bool
    {
        return $travelOrder->update($data);
    }
    
    public function delete(TravelOrder $travelOrder): bool
    {
        return $travelOrder->delete();
    }
} 