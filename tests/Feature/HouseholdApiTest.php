<?php

namespace Tests\Feature;

use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HouseholdApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_household(): void
    {
        // payload for create household
        $payload = [
            'owner_name' => 'Budi Santoso',
            'address'    => 'Jl. Melati No. 1',
            'block'      => 'A',
            'no'         => '01',
        ];

        // call create method
        $response = $this->postJson('/api/households', $payload);

        // assert response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'owner_name',
                    'address',
                    'block',
                    'no',
                ],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.owner_name', 'Budi Santoso');
    }

    public function test_cannot_create_household_without_required_fields(): void
    {
        // call create method
        $response = $this->postJson('/api/households', []);

        // assert response
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['owner_name', 'address']);
    }

    public function test_can_list_households(): void
    {
        // create household
        Household::create([
            'owner_name' => 'Siti Rahayu',
            'address'    => 'Jl. Mawar No. 5',
        ]);

        // call list method
        $response = $this->getJson('/api/households');

        // assert response
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_can_get_household_detail(): void
    {
        // create household
        $household = Household::create([
            'owner_name' => 'Ahmad Fauzi',
            'address'    => 'Jl. Anggrek No. 10',
        ]);

        // call get method
        $response = $this->getJson("/api/households/{$household->id}");

        // assert response
        $response->assertStatus(200)
            ->assertJsonPath('data.owner_name', 'Ahmad Fauzi');
    }

    public function test_returns_404_for_nonexistent_household(): void
    {
        // call get method
        $response = $this->getJson('/api/households/nonexistentid123');

        // assert response
        $response->assertStatus(404);
    }

    public function test_can_update_household(): void
    {
        // create household
        $household = Household::create([
            'owner_name' => 'Lama Name',
            'address'    => 'Jl. Lama No. 1',
        ]);

        // call update method
        $response = $this->putJson("/api/households/{$household->id}", [
            'owner_name' => 'Nama Baru',
        ]);

        // assert response
        $response->assertStatus(200)
            ->assertJsonPath('data.owner_name', 'Nama Baru');
    }

    public function test_can_delete_household(): void
    {
        // create household
        $household = Household::create([
            'owner_name' => 'Akan Dihapus',
            'address'    => 'Jl. Test No. 1',
        ]);

        // call delete method
        $response = $this->deleteJson("/api/households/{$household->id}");

        // assert response
        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
