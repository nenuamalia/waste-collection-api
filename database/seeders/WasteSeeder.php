<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Waste;
use Illuminate\Database\Seeder;

class WasteSeeder extends Seeder
{
    public function run(): void
    {
        $households = Household::all();

        if ($households->isEmpty()) {
            $this->command->warn('Run HouseholdSeeder first!');
            return;
        }

        $types = ['organic', 'plastic', 'paper', 'electronic'];

        foreach ($households as $household) {
            foreach ($types as $type) {
                $data = [
                    'household_id' => (string) $household->id,
                    'type'         => $type,
                    'status'       => 'pending',
                    'pickup_date'  => null,
                ];

                if ($type === 'electronic') {
                    $data['safety_check'] = false;
                }

                Waste::create($data);
            }
        }

        $this->command->info('Total waste created: ' . Waste::count());
    }
}
