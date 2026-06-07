<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
         
            'trip_id'      => Trip::factory(),
            'driver_id'    => User::factory(),
            'passenger_id' => User::factory(),
            'rating'       => fake()->numberBetween(1, 5),
            'comment'      => fake()->optional()->sentence(),
        ];
    }
}
