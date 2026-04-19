<?php

namespace App\Factories;

use App\Models\Waste;
use App\Models\WasteOrganic;
use App\Models\WastePlastic;
use App\Models\WastePaper;
use App\Models\WasteElectronic;

class WasteFactory
{
    /**
     * Create a new Waste instance based on type
     */
    public static function make(string $type, array $attributes = []): Waste
    {
        return match($type) {
            'organic'    => new WasteOrganic($attributes),
            'plastic'    => new WastePlastic($attributes),
            'paper'      => new WastePaper($attributes),
            'electronic' => new WasteElectronic($attributes),
            default      => throw new \InvalidArgumentException("Invalid waste type: $type"),
        };
    }

    /**
     * Resolve existing Waste document from DB to the correct subclass
     */
    public static function resolve(Waste $waste): Waste
    {
        return match($waste->type) {
            'organic'    => (new WasteOrganic())->forceFill($waste->toArray()),
            'plastic'    => (new WastePlastic())->forceFill($waste->toArray()),
            'paper'      => (new WastePaper())->forceFill($waste->toArray()),
            'electronic' => (new WasteElectronic())->forceFill($waste->toArray()),
            default      => $waste,
        };
    }
}
