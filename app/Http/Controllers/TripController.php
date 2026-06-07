<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    // Passenger - create a new trip request
    public function store(StoreTripRequest $request)
    {
        $trip = $request->user()->trips()->create([
            ...$request->validated(),
            'status' => 'requested',
        ]);

        return new TripResource($trip);
    }

    // Driver - list trips that are still open (no driver yet)
    public function available()
    {
        $trips = Trip::where('status', 'requested')->whereNull('driver_id')->get();

        return TripResource::collection($trips);
    }

    // Driver - acept a request trip
    public function accept(Request $request, Trip $trip)
    {
        $this->authorize('accept', $trip);

        $trip->update([
            'driver_id'   => $request->user()->id,
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);

        return new TripResource($trip);
    }

    // Driver - complete a trip they accepted
    public function complete(Request $request, Trip $trip)
    {
        $this->authorize('complete', $trip);

        $trip->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        return new TripResource($trip);
    }

    // Passenger- cancel their own trip
    public function cancel(Request $request, Trip $trip)
    {
        $this->authorize('cancel', $trip);

        $trip->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return new TripResource($trip);
    }
}
