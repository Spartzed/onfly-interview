<?php

namespace Database\Seeders;

use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Domain\User\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TravelOrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            // Pedidos solicitados
            TravelOrder::create([
                'order_id' => 'TO-' . strtoupper(Str::random(8)),
                'requester_name' => $user->name,
                'destination' => 'São Paulo, SP',
                'departure_date' => now()->addDays(30),
                'return_date' => now()->addDays(35),
                'status' => TravelOrderStatus::REQUESTED,
                'user_id' => $user->id,
            ]);

            TravelOrder::create([
                'order_id' => 'TO-' . strtoupper(Str::random(8)),
                'requester_name' => $user->name,
                'destination' => 'Rio de Janeiro, RJ',
                'departure_date' => now()->addDays(45),
                'return_date' => now()->addDays(50),
                'status' => TravelOrderStatus::APPROVED,
                'user_id' => $user->id,
            ]);

            TravelOrder::create([
                'order_id' => 'TO-' . strtoupper(Str::random(8)),
                'requester_name' => $user->name,
                'destination' => 'Brasília, DF',
                'departure_date' => now()->addDays(15),
                'return_date' => now()->addDays(18),
                'status' => TravelOrderStatus::CANCELLED,
                'user_id' => $user->id,
            ]);
        }
    }
} 