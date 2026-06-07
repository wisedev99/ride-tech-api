<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    public function accept(User $user, Trip $trip): bool
    {
        return $trip->status === 'requested' && $trip->driver_id === null;
    }

    public function complete(User $user, Trip $trip): bool
    {
        return $user->id === $trip->driver_id;
    }

    public function cancel(User $user, Trip $trip): bool
    {
        return $user->id === $trip->passenger_id;
    }

    public function review(User $user, Trip $trip): bool
    {
        return $user->id === $trip->passenger_id && $trip->status === 'completed';
    }
}
