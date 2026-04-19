<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WasteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'household_id' => $this->household_id,
            'type'         => $this->type,
            'status'       => $this->status,
            'pickup_date'  => $this->pickup_date,
            'safety_check' => $this->when(
                $this->type === 'electronic',
                $this->safety_check
            ),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
