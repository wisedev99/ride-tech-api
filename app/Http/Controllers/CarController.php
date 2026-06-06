<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    // List the logged-in driver's cars
    public function index(Request $request)
    {
        return CarResource::collection($request->user()->cars);
    }

    // Add a car for the logged-in driver
    public function store(StoreCarRequest $request)
    {
        $car = $request->user()->cars()->create($request->validated());

        return new CarResource($car);
    }

    // Delete one of the driver's own cars
    public function destroy(Request $request, Car $car)
    {
        if ($car->driver_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $car->delete();

        return response()->json(['message' => 'Car deleted.']);
    }
}
