<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Trip;
use App\Models\User;

class ReviewController extends Controller
{
    // List a driver's reviews
    public function index(User $driver)
    {
        $reviews = Review::where('driver_id', $driver->id)
            ->latest()
            ->paginate(15);

        return ReviewResource::collection($reviews);
    }


    public function store(StoreReviewRequest $request, User $driver)
    {
        $passenger = $request->user();

        // the passenger may only review a driver they actually rode with
        $trip = Trip::where('passenger_id', $passenger->id)
            ->where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        abort_unless($trip, 403, 'You can only review a driver after a completed trip.');

        $review = Review::updateOrCreate(
            ['trip_id' => $trip->id, 'passenger_id' => $passenger->id],
            [...$request->validated(), 'driver_id' => $driver->id],
        );

        return new ReviewResource($review);
    }
}
