<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Trip;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Passenger - review the driver of a completed trip
    public function store(StoreReviewRequest $request, Trip $trip)
    {
        $this->authorize('review', $trip);

        $review = $trip->reviews()->create([
            ...$request->validated(),
            'passenger_id' => $request->user()->id,
            'driver_id'    => $trip->driver_id,
        ]);

        return new ReviewResource($review);
    }
}
