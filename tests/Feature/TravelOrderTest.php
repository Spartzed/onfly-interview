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
        
        // Login para obter o token
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $token = $loginResponse->json('access_token');

        $orderData = [
            'requester_name' => 'Test User',
            'destination' => 'Test Destination',
            'departure_date' => now()->addDay()->toDateString(),
            'return_date' => now()->addDays(5)->toDateString(),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/travel-orders', $orderData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
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
        
        // Login para obter o token
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $token = $loginResponse->json('access_token');

        TravelOrder::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/travel-orders');

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
                'meta' => [
                    'total',
                    'user_role',
                ],
            ]);
    }

    public function test_user_can_view_travel_order()
    {
        $user = User::factory()->create();
        
        // Login para obter o token
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $token = $loginResponse->json('access_token');

        $order = TravelOrder::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/travel-orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $order->id,
                    'destination' => $order->destination,
                ],
            ]);
    }
}

