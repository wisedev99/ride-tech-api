<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    // List the authenticated user's trips (as passenger or driver) with filters
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $trips = Trip::query()
            ->where(function ($q) use ($userId) {
                $q->where('passenger_id', $userId)
                  ->orWhere('driver_id', $userId);
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('passenger_id'), fn ($q) => $q->where('passenger_id', $request->passenger_id))
            ->when($request->filled('driver_id'), fn ($q) => $q->where('driver_id', $request->driver_id))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return TripResource::collection($trips);
    }

    // Passenger - create a new trip request
    public function store(StoreTripRequest $request)
    {
        $trip = $request->user()->trips()->create([
            ...$request->validated(),
            'status' => 'requested',
        ]);

        return new TripResource($trip);
    }

    // Show one trip - only its passenger or driver may view it
    public function show(Trip $trip)
    {
        $this->authorize('view', $trip);

        return new TripResource($trip);
    }

    // Passenger - update a trip while it's still 'requested'
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        $this->authorize('update', $trip);

        $trip->update($request->validated());

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

    // Driver - reject an open trip request
    public function reject(Request $request, Trip $trip)
    {
        $this->authorize('reject', $trip);

        $trip->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
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
