<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $household = Household::first();

        if (!$household) {
            $this->command->warn('Run HouseholdSeeder first!');
            return;
        }

        Payment::create([
            'household_id' => (string) $household->id,
            'amount'       => 50000,
            'status'       => 'paid',
            'payment_date' => now(),
        ]);

        Payment::create([
            'household_id' => (string) $household->id,
            'amount'       => 50000,
            'status'       => 'pending',
            'payment_date' => null,
        ]);

        $this->command->info('Total payment created: ' . Payment::count());
    }
}
