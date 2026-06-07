<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;

class CarPolicy
{
    public function delete(User $user, Car $car): bool
    {
        return $user->id === $car->driver_id;
    }
}
