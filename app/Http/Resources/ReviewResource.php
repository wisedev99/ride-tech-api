<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        
        return [
            'id'           => $this->id,
            'rating'       => $this->rating,
            'comment'      => $this->comment,
            'trip_id'      => $this->trip_id,
            'driver_id'    => $this->driver_id,
            'passenger_id' => $this->passenger_id,
        ];
    }
}
