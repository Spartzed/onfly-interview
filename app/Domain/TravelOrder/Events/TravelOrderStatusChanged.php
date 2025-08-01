<?php

namespace App\Domain\TravelOrder\Events;

use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TravelOrderStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TravelOrder $travelOrder
    ) {}
} 