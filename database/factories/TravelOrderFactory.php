<?php

namespace Database\Factories;

use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TravelOrderFactory extends Factory
{
    protected $model = TravelOrder::class;

    public function definition(): array
    {
        return [
            'order_id' => 'TO-' . strtoupper(Str::random(8)),
            'requester_name' => fake()->name(),
            'destination' => fake()->city() . ', ' . fake()->stateAbbr(),
            'departure_date' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'return_date' => fake()->dateTimeBetween('+2 months', '+3 months'),
            'status' => TravelOrderStatus::REQUESTED,
            'user_id' => User::factory(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TravelOrderStatus::APPROVED,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TravelOrderStatus::CANCELLED,
        ]);
    }
} 