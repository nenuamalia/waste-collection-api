<?php

namespace App\Console\Commands;

use App\Repositories\WasteRepository;
use Illuminate\Console\Command;

class AutoCancelOrganicWaste extends Command
{
    protected $signature   = 'waste:auto-cancel-organic';
    protected $description = 'Auto-cancel organic waste pickups older than 3 days';

    public function __construct(
        protected WasteRepository $wasteRepository
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $wastes = $this->wasteRepository->getPendingOrganicOlderThan(3);

        foreach ($wastes as $waste) {
            $this->wasteRepository->update($waste->id, ['status' => 'canceled']);
            $this->info("Auto-canceled organic waste ID: {$waste->id}");
        }

        $this->info("Total auto-canceled: {$wastes->count()} pickup(s).");
    }
}
