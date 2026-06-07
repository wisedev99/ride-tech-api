<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Review;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        
        $drivers    = $this->ensureUsers('driver', 3);
        $passengers = $this->ensureUsers('passenger', 5);

        $drivers->each(function (User $driver) {
            if ($driver->cars()->doesntExist()) {
                Car::factory()->create(['driver_id' => $driver->id]);
            }
        });


        $passengers->each(function (User $passenger) {
            if ($passenger->trips()->doesntExist()) {
                Trip::factory()->create(['passenger_id' => $passenger->id]);
            }
        });


        $passengers->take(3)->each(function (User $passenger) use ($drivers) {
            if ($passenger->reviewsWritten()->exists()) {
                return;
            }

            $driver = $drivers->random();

            $trip = Trip::factory()->completed()->create([
                'passenger_id' => $passenger->id,
                'driver_id'    => $driver->id,
            ]);

            Review::factory()->create([
                'trip_id'      => $trip->id,
                'passenger_id' => $passenger->id,
                'driver_id'    => $driver->id,
            ]);
        });
    }


    private function ensureUsers(string $role, int $target): Collection
    {
        $users = User::role($role)->get();

        if ($users->count() < $target) {
            $missing = User::factory($target - $users->count())
                ->create()
                ->each(fn (User $user) => $user->assignRole($role));

            $users = $users->merge($missing);
        }

        return $users;
    }
}
