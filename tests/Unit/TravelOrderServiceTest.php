<?php

namespace Tests\Unit;

use App\Application\Services\TravelOrderService;
use App\Domain\TravelOrder\Entities\TravelOrder;
use App\Domain\TravelOrder\Enums\TravelOrderStatus;
use App\Domain\TravelOrder\Repositories\TravelOrderRepositoryInterface;
use App\Application\Services\ResponseService;
use App\Application\Services\NotificationService;
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
    private $mockResponseService;
    private $mockNotificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = Mockery::mock(TravelOrderRepositoryInterface::class);
        $this->mockResponseService = Mockery::mock(ResponseService::class);
        $this->mockNotificationService = Mockery::mock(NotificationService::class);
        $this->service = new TravelOrderService(
            $this->mockRepository,
            $this->mockResponseService,
            $this->mockNotificationService
        );
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
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);
        
        $user = User::factory()->create([
            'role' => 'user'
        ]);
        
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => TravelOrderStatus::REQUESTED,
        ]);

        $this->mockNotificationService
            ->shouldReceive('createTravelOrderNotification')
            ->once()
            ->with($travelOrder, 'approved');

        $result = $this->service->updateTravelOrderStatus(
            $travelOrder,
            TravelOrderStatus::APPROVED,
            $admin
        );

        $this->assertTrue($result);
    }

    public function test_regular_user_cannot_update_travel_order_status()
    {
        $regularUser = User::factory()->create([
            'role' => 'user'
        ]);
        
        $adminUser = User::factory()->create([
            'role' => 'admin'
        ]);
        
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $adminUser->id,
            'status' => TravelOrderStatus::REQUESTED,
        ]);

        $this->mockNotificationService
            ->shouldReceive('createTravelOrderNotification')
            ->never();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Você não tem permissão para alterar o status deste pedido.');

        $this->service->updateTravelOrderStatus(
            $travelOrder,
            TravelOrderStatus::APPROVED,
            $regularUser
        );
    }

    public function test_cancel_travel_order()
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);
        
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => TravelOrderStatus::REQUESTED,
        ]);

        $this->mockNotificationService
            ->shouldReceive('createTravelOrderNotification')
            ->once()
            ->with($travelOrder, 'cancelled');

        $result = $this->service->cancelTravelOrder($travelOrder, $user);

        $this->assertTrue($result);
        $this->assertEquals(TravelOrderStatus::CANCELLED, $travelOrder->status);
    }

    public function test_admin_can_update_own_travel_order_status()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);
        
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $admin->id,
            'status' => TravelOrderStatus::REQUESTED,
        ]);

        $this->mockNotificationService
            ->shouldReceive('createTravelOrderNotification')
            ->once()
            ->with($travelOrder, 'approved');

        $result = $this->service->updateTravelOrderStatus(
            $travelOrder,
            TravelOrderStatus::APPROVED,
            $admin
        );

        $this->assertTrue($result);
        $this->assertEquals(TravelOrderStatus::APPROVED, $travelOrder->status);
    }

    public function test_cannot_cancel_approved_travel_order()
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);
        
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status' => TravelOrderStatus::APPROVED,
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