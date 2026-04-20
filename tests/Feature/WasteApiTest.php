<?php

namespace Tests\Feature;

use App\Models\Household;
use App\Models\Payment;
use App\Models\User;
use App\Models\Waste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WasteApiTest extends TestCase
{
    use RefreshDatabase;

    private Household $household;
    private User $user;

    protected function setUp(): void
    {
        // setup test
        parent::setUp();

        // create user for JWT authentication
        $this->user = User::create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // create household
        $this->household = Household::create([
            'owner_name' => 'Test User',
            'address'    => 'Jl. Test No. 1',
        ]);
    }

    public function test_can_create_pickup_request(): void
    {
        // call create method (authenticated)
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/pickups', [
                'household_id' => (string) $this->household->id,
                'type'         => 'plastic',
            ]);

        // assert response
        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.type', 'plastic')
            ->assertJsonPath('data.status', 'pending');
    }

    public function test_cannot_create_pickup_if_has_unpaid_payment(): void
    {
        // create unpaid payment
        Payment::create([
            'household_id' => (string) $this->household->id,
            'amount'       => 50000,
            'status'       => 'pending',
        ]);

        // call create method (authenticated)
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/pickups', [
                'household_id' => (string) $this->household->id,
                'type'         => 'plastic',
            ]);

        // must be rejected
        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_can_schedule_pickup(): void
    {
        // create waste
        $waste = Waste::create([
            'household_id' => (string) $this->household->id,
            'type'         => 'plastic',
            'status'       => 'pending',
        ]);

        // call schedule method (authenticated)
        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/pickups/{$waste->id}/schedule", [
                'pickup_date' => now()->addDay()->toDateTimeString(),
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'scheduled');
    }

    public function test_cannot_schedule_electronic_without_safety_check(): void
    {
        // create waste electronic
        $waste = Waste::create([
            'household_id' => (string) $this->household->id,
            'type'         => 'electronic',
            'status'       => 'pending',
            'safety_check' => false,
        ]);

        // call schedule method (authenticated)
        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/pickups/{$waste->id}/schedule", [
                'pickup_date' => now()->addDay()->toDateTimeString(),
            ]);

        // electronic must have safety_check
        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_complete_pickup_generates_payment(): void
    {
        // create waste
        $waste = Waste::create([
            'household_id' => (string) $this->household->id,
            'type'         => 'plastic',
            'status'       => 'scheduled',
            'pickup_date'  => now()->toDateTimeString(),
        ]);

        // call complete method (authenticated)
        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/pickups/{$waste->id}/complete");

        // assert response
        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'completed');

        // payment automatically created (query MongoDB directly)
        $payment = Payment::where('household_id', (string) $this->household->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(50000, $payment->amount);
        $this->assertEquals('pending', $payment->status);
    }

    public function test_electronic_complete_generates_higher_payment(): void
    {
        // create waste electronic
        $waste = Waste::create([
            'household_id' => (string) $this->household->id,
            'type'         => 'electronic',
            'status'       => 'scheduled',
            'safety_check' => true,
            'pickup_date'  => now()->toDateTimeString(),
        ]);

        // call complete method (authenticated)
        $this->actingAs($this->user, 'api')
            ->putJson("/api/pickups/{$waste->id}/complete");

        // Electronic = Rp 100.000 (query MongoDB directly)
        $payment = Payment::where('household_id', (string) $this->household->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(100000, $payment->amount);
    }

    public function test_can_cancel_pickup(): void
    {
        // create waste
        $waste = Waste::create([
            'household_id' => (string) $this->household->id,
            'type'         => 'paper',
            'status'       => 'pending',
        ]);

        // call cancel method (authenticated)
        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/pickups/{$waste->id}/cancel");

        // assert response
        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'canceled');
    }
}
