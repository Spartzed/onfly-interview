<?php

namespace Tests\Unit;

use App\Application\Services\TravelOrderService;
use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Domain\TravelOrder\Repositories\TravelOrderRepositoryInterface;
use App\Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TravelOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private TravelOrderService $service;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = Mockery::mock(TravelOrderRepositoryInterface::class);
        $this->service = new TravelOrderService($this->mockRepository);
    }

    public function test_create_travel_order()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);
        
        $data = [
            'requester_name' => 'John Doe',
            'destination' => 'São Paulo',
            'departure_date' => '2024-02-01',
            'return_date' => '2024-02-05',
        ];

        $expectedTravelOrder = new TravelOrder($data);
        $expectedTravelOrder->id = 1;
        $expectedTravelOrder->order_id = 'TO-ABC12345';
        $expectedTravelOrder->user_id = $user->id;
        $expectedTravelOrder->status = TravelOrderStatus::REQUESTED;

        $this->mockRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn($expectedTravelOrder);

        $result = $this->service->createTravelOrder($data, $user);

        $this->assertInstanceOf(TravelOrder::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals(TravelOrderStatus::REQUESTED, $result->status);
    }

    public function test_get_user_travel_orders()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);
        
        $expectedCollection = new Collection();

        $this->mockRepository
            ->shouldReceive('findByUser')
            ->once()
            ->with($user)
            ->andReturn($expectedCollection);

        $result = $this->service->getUserTravelOrders($user);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_update_travel_order_status_as_admin()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);
        
        $travelOrder = TravelOrder::create([
            'order_id' => 'TO-TEST123',
            'requester_name' => 'Test User',
            'destination' => 'São Paulo',
            'departure_date' => '2024-02-01',
            'return_date' => '2024-02-05',
            'status' => TravelOrderStatus::REQUESTED,
            'user_id' => $user->id,
        ]);

        $this->mockRepository
            ->shouldReceive('findById')
            ->once()
            ->with($travelOrder->id)
            ->andReturn($travelOrder);

        $result = $this->service->updateTravelOrderStatus(
            $travelOrder,
            TravelOrderStatus::APPROVED,
            $admin
        );

        $this->assertTrue($result);
    }

    public function test_cannot_update_own_travel_order_status()
    {
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        
        $travelOrder = TravelOrder::create([
            'order_id' => 'TO-TEST123',
            'requester_name' => 'Test User',
            'destination' => 'São Paulo',
            'departure_date' => '2024-02-01',
            'return_date' => '2024-02-05',
            'status' => TravelOrderStatus::REQUESTED,
            'user_id' => $user->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Você não tem permissão para alterar o status deste pedido.');

        $this->service->updateTravelOrderStatus(
            $travelOrder,
            TravelOrderStatus::APPROVED,
            $user
        );
    }

    public function test_cancel_travel_order()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);
        
        $travelOrder = TravelOrder::create([
            'order_id' => 'TO-TEST123',
            'requester_name' => 'Test User',
            'destination' => 'São Paulo',
            'departure_date' => '2024-02-01',
            'return_date' => '2024-02-05',
            'status' => TravelOrderStatus::REQUESTED,
            'user_id' => $user->id,
        ]);

        $result = $this->service->cancelTravelOrder($travelOrder, $user);

        $this->assertTrue($result);
        $this->assertEquals(TravelOrderStatus::CANCELLED, $travelOrder->status);
    }

    public function test_cannot_cancel_approved_travel_order()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);
        
        $travelOrder = TravelOrder::create([
            'order_id' => 'TO-TEST123',
            'requester_name' => 'Test User',
            'destination' => 'São Paulo',
            'departure_date' => '2024-02-01',
            'return_date' => '2024-02-05',
            'status' => TravelOrderStatus::APPROVED,
            'user_id' => $user->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Apenas pedidos solicitados podem ser cancelados.');

        $this->service->cancelTravelOrder($travelOrder, $user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 