<?php

namespace Tests\Unit;

use App\Models\Waste;
use App\Models\WastePlastic;
use App\Models\WasteElectronic;
use App\Repositories\HouseholdRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\WasteRepository;
use App\Services\WasteService;
use Exception;
use Mockery;
use Tests\TestCase;

class WasteServiceTest extends TestCase
{
    private WasteService $service;
    private $wasteRepo;
    private $paymentRepo;
    private $householdRepo;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock all repository
        $this->wasteRepo     = Mockery::mock(WasteRepository::class);
        $this->paymentRepo   = Mockery::mock(PaymentRepository::class);
        $this->householdRepo = Mockery::mock(HouseholdRepository::class);

        // inject mock repository
        $this->service = new WasteService(
            $this->wasteRepo,
            $this->paymentRepo,
            $this->householdRepo,
        );
    }

    protected function tearDown(): void
    {
        // close mockery
        Mockery::close();
        parent::tearDown();
    }

    public function test_cannot_create_pickup_if_has_unpaid_payment(): void
    {
        // mock payment repo
        $this->paymentRepo
            ->shouldReceive('hasUnpaidByHousehold')
            ->with('household123')
            ->andReturn(true);

        // expect exception
        $this->expectException(Exception::class);
        $this->expectExceptionCode(422);

        // call create method
        $this->service->create([
            'household_id' => 'household123',
            'type'         => 'plastic',
        ]);
    }

    public function test_complete_pickup_creates_payment_with_correct_amount(): void
    {
        // simulate waste plastic that already scheduled
        $waste = new WastePlastic();
        $waste->forceFill([
            '_id'          => 'waste123',
            'household_id' => 'household123',
            'type'         => 'plastic',
            'status'       => 'scheduled',
        ]);

        // simulate waste plastic that already completed
        $completedWaste = new WastePlastic();
        $completedWaste->forceFill([
            '_id'          => 'waste123',
            'household_id' => 'household123',
            'type'         => 'plastic',
            'status'       => 'completed',
        ]);

        // mock waste repo
        $this->wasteRepo
            ->shouldReceive('findById')
            ->with('waste123')
            ->andReturn($waste);

        // mock waste repo update
        $this->wasteRepo
            ->shouldReceive('update')
            ->andReturn($completedWaste);

        // mock payment repo
        $paymentData = null;

        $this->paymentRepo
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use (&$paymentData) {
                $paymentData = $data;
                return true;
            }));

        // call complete method
        $this->service->complete('waste123');

        // assert payment data
        $this->assertNotNull($paymentData);
        $this->assertEquals(50000, $paymentData['amount']);
        $this->assertEquals('pending', $paymentData['status']);
        $this->assertEquals('household123', $paymentData['household_id']);
    }

    public function test_electronic_complete_creates_payment_100000(): void
    {
        // simulate waste electronic that already scheduled
        $waste = new WasteElectronic();
        $waste->forceFill([
            '_id'          => 'waste456',
            'household_id' => 'household123',
            'type'         => 'electronic',
            'status'       => 'scheduled',
            'safety_check' => true,
        ]);

        // simulate waste electronic that already completed
        $completedWaste = new WasteElectronic();
        $completedWaste->forceFill([
            '_id'          => 'waste456',
            'household_id' => 'household123',
            'type'         => 'electronic',
            'status'       => 'completed',
        ]);

        // mock waste repo
        $this->wasteRepo
            ->shouldReceive('findById')
            ->andReturn($waste);

        // mock waste repo update
        $this->wasteRepo
            ->shouldReceive('update')
            ->andReturn($completedWaste);

        $paymentData = null;

        // mock payment repo
        $this->paymentRepo
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) use (&$paymentData) {
                $paymentData = $data;
                return true;
            }));

        // call complete method
        $this->service->complete('waste456');

        // assert payment data
        $this->assertNotNull($paymentData);
        $this->assertEquals(100000, $paymentData['amount']);
    }
}
