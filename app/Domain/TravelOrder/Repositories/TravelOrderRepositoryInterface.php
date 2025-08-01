<?php

namespace App\Domain\TravelOrder\Repositories;

use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;

interface TravelOrderRepositoryInterface
{
    public function create(array $data): TravelOrder;
    
    public function findById(int $id): ?TravelOrder;
    
    public function findByOrderId(string $orderId): ?TravelOrder;
    
    public function findByUser(User $user): Collection;
    
    public function findByStatus(TravelOrderStatus $status): Collection;
    
    public function findByDateRange(string $startDate, string $endDate): Collection;
    
    public function findByDestination(string $destination): Collection;
    
    public function update(TravelOrder $travelOrder, array $data): bool;
    
    public function delete(TravelOrder $travelOrder): bool;
} 