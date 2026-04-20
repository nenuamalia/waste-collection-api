<?php

namespace Tests\Unit;

use App\Models\WasteElectronic;
use App\Models\WasteOrganic;
use App\Models\WastePlastic;
use Tests\TestCase;

class WasteElectronicTest extends TestCase
{
    public function test_electronic_cannot_be_scheduled_without_safety_check(): void
    {
        // simulate waste electronic that not completed
        $waste = new WasteElectronic();
        $waste->forceFill([
            'status'       => 'pending',
            'safety_check' => false,
        ]);

        // assert waste electronic cannot be scheduled
        $this->assertFalse($waste->canBeScheduled());
    }

    public function test_electronic_can_be_scheduled_with_safety_check(): void
    {
        // simulate waste electronic that completed
        $waste = new WasteElectronic();
        $waste->forceFill([
            'status'       => 'pending',
            'safety_check' => true,
        ]);

        // assert waste electronic can be scheduled
        $this->assertTrue($waste->canBeScheduled());
    }

    public function test_electronic_payment_amount_is_100000(): void
    {
        // simulate waste electronic
        $waste = new WasteElectronic();
        $this->assertEquals(100000, $waste->getPaymentAmount());
    }

    public function test_plastic_payment_amount_is_50000(): void
    {
        // simulate waste plastic
        $waste = new WastePlastic();
        $this->assertEquals(50000, $waste->getPaymentAmount());
    }

    public function test_organic_should_auto_cancel_after_3_days(): void
    {
        // simulate waste organic that not completed
        $waste = new WasteOrganic();
        $waste->forceFill([
            'status'     => 'pending',
            'created_at' => now()->subDays(4), // 4 days ago
        ]);

        // assert waste organic should auto cancel after 3 days
        $this->assertTrue($waste->shouldAutoCancelOrganicPickup());
    }

    public function test_organic_should_not_cancel_before_3_days(): void
    {
        // simulate waste organic that not completed
        $waste = new WasteOrganic();
        $waste->forceFill([
            'status'     => 'pending',
            'created_at' => now()->subDays(2), // 2 days ago
        ]);

        // assert waste organic should not cancel before 3 days
        $this->assertFalse($waste->shouldAutoCancelOrganicPickup());
    }
}
