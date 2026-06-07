<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    public function definition(): array
    {
       
        [$brand, $model] = fake()->randomElement([
            ['Mercedes-Benz', 'E-Class'],
            ['BMW', '3 Series'],
            ['Lexus', 'RX'],
            ['Hyundai', 'Sonata'],
            ['Hyundai', 'Elantra'],
            ['Toyota', 'Camry'],
            ['Kia', 'K5'],
        ]);

        return [
            'driver_id'    => User::factory(),
            'brand'        => $brand,
            'model'        => $model,
            'plate_number' => strtoupper(fake()->unique()->bothify('01###??')),
        ];
    }
}
