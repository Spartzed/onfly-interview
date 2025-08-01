<?php

namespace App\Domain\TravelOrder\Listeners;

use App\Domain\TravelOrder\Events\TravelOrderStatusChanged;
use App\Domain\TravelOrder\Notifications\TravelOrderStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTravelOrderStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TravelOrderStatusChanged $event): void
    {
        $travelOrder = $event->travelOrder;
        $user = $travelOrder->user;

        if ($user) {
            $user->notify(new TravelOrderStatusNotification($travelOrder, $travelOrder->status));
        }
    }
} 