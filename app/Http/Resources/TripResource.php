<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        
        return [
            'id'                  => $this->id,
            'pickup_address'      => $this->pickup_address,
            'destination_address' => $this->destination_address,
            'preferences'         => $this->preferences,
            'status'              => $this->status,
            'passenger_id'        => $this->passenger_id,
            'driver_id'           => $this->driver_id,
            'accepted_at'         => $this->accepted_at,
            'completed_at'        => $this->completed_at,
            'cancelled_at'        => $this->cancelled_at,
        ];
    }
}
