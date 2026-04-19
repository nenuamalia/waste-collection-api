<?php

namespace Database\Seeders;

use App\Models\Household;
use Illuminate\Database\Seeder;

class HouseholdSeeder extends Seeder
{
    public function run(): void
    {
        $households = [
            [
                'owner_name' => 'Budi Santoso',
                'address'    => 'Jl. Melati No. 1',
                'block'      => 'A',
                'no'         => '01',
            ],
            [
                'owner_name' => 'Siti Rahayu',
                'address'    => 'Jl. Mawar No. 5',
                'block'      => 'A',
                'no'         => '05',
            ],
            [
                'owner_name' => 'Ahmad Fauzi',
                'address'    => 'Jl. Anggrek No. 10',
                'block'      => 'B',
                'no'         => '10',
            ],
        ];

        foreach ($households as $data) {
            Household::create($data);
        }

        $this->command->info('Total household created: ' . Household::count());
    }
}
