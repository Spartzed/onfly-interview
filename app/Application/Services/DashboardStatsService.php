<?php

namespace App\Application\Services;

use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use Illuminate\Http\JsonResponse;

class DashboardStatsService
{
    public function getStats(): JsonResponse
    {
        $totalOrders = TravelOrder::count();
        $approvedOrders = TravelOrder::where('status', TravelOrderStatus::APPROVED)->count();
        $requestedOrders = TravelOrder::where('status', TravelOrderStatus::REQUESTED)->count();
        $cancelledOrders = TravelOrder::where('status', TravelOrderStatus::CANCELLED)->count();

        return response()->json([
            'total_orders' => $totalOrders,
            'approved_orders' => $approvedOrders,
            'requested_orders' => $requestedOrders,
            'cancelled_orders' => $cancelledOrders,
        ]);
    }
}
