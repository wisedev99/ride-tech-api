<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    public function definition(): array
    {
        return [

            'passenger_id'        => User::factory(),
            'driver_id'           => null,
            'pickup_address'      => fake()->streetAddress(),
            'destination_address' => fake()->streetAddress(),
            'preferences'         => fake()->optional()->sentence(),
            'status'              => 'requested',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn  () =>  [
            'driver_id'    => User::factory(),
            'status'       => 'completed',
            'accepted_at'  => now()->subHour(),
            'completed_at' => now(),
        ]);
    }
}
