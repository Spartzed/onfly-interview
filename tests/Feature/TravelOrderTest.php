<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Domain\User\Entities\User;
use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use Laravel\Sanctum\Sanctum;

class TravelOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_travel_order()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $orderData = [
            'requester_name' => 'Test User',
            'destination' => 'Test Destination',
            'departure_date' => now()->addDay()->toDateString(),
            'return_date' => now()->addDays(5)->toDateString(),
        ];

        $response = $this->postJson('/api/travel-orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'travel_order' => [
                    'id',
                    'requester_name',
                    'destination',
                    'departure_date',
                    'return_date',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('travel_orders', [
            'destination' => 'Test Destination',
        ]);
    }

    public function test_user_can_list_travel_orders()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        TravelOrder::factory()->count(3)->create();

        $response = $this->getJson('/api/travel-orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'requester_name',
                        'destination',
                        'departure_date',
                        'return_date',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_user_can_view_travel_order()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $order = TravelOrder::factory()->create();

        $response = $this->getJson("/api/travel-orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $order->id,
                    'destination' => $order->destination,
                ],
            ]);
    }
}

