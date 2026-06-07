<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RideFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function passenger(): User
    {
        $user = User::factory()->create();
        $user->assignRole('passenger');

        return $user;
    }

    private function driver(): User
    {
        $user = User::factory()->create();
        $user->assignRole('driver');

        return $user;
    }

    /** 1. Auth: register then login both return a token. */
    public function test_register_and_login_return_a_token(): void
    {
        $this->postJson('/api/register', [
            'name'                  => 'Bob',
            'email'                 => 'bob@example.com',
            'phone'                 => '+998901112233',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'passenger',
        ])->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'name', 'roles']]);

        $this->postJson('/api/login', [
            'email'    => 'bob@example.com',
            'password' => 'secret123',
        ])->assertOk()->assertJsonStructure(['token']);
    }

    /** 2. A passenger can create a trip; a driver cannot. */
    public function test_only_a_passenger_can_create_a_trip(): void
    {
        Sanctum::actingAs($this->passenger());
        $this->postJson('/api/trips', [
            'pickup_address'      => 'Chilonzor',
            'destination_address' => 'Yunusobod',
        ])->assertCreated()->assertJsonPath('data.status', 'requested');

        Sanctum::actingAs($this->driver());
        $this->postJson('/api/trips', [
            'pickup_address'      => 'Chilonzor',
            'destination_address' => 'Yunusobod',
        ])->assertForbidden();
    }

    /** 3. Full lifecycle: driver accepts then completes a trip. */
    public function test_driver_can_accept_then_complete_a_trip(): void
    {
        $passenger = $this->passenger();
        $driver    = $this->driver();
        $trip      = Trip::factory()->create(['passenger_id' => $passenger->id]);

        Sanctum::actingAs($driver);

        $this->postJson("/api/trips/{$trip->id}/accept")
            ->assertOk()
            ->assertJsonPath('data.status', 'accepted')
            ->assertJsonPath('data.driver_id', $driver->id);

        $this->postJson("/api/trips/{$trip->id}/complete")
            ->assertOk()
            ->assertJsonPath('data.status', 'completed');
    }

    /** 4. A passenger cannot accept a trip (driver-only action). */
    public function test_passenger_cannot_accept_a_trip(): void
    {
        $passenger = $this->passenger();
        $trip      = Trip::factory()->create(['passenger_id' => $passenger->id]);

        Sanctum::actingAs($passenger);
        $this->postJson("/api/trips/{$trip->id}/accept")->assertForbidden();
    }

    /** 5. GET /api/trips returns only the caller's own trips and honours filters. */
    public function test_index_lists_only_my_trips_with_filters(): void
    {
        $me     = $this->passenger();
        $other  = $this->passenger();

        Trip::factory()->create(['passenger_id' => $me->id, 'status' => 'requested']);
        Trip::factory()->create(['passenger_id' => $me->id, 'status' => 'completed']);
        Trip::factory()->create(['passenger_id' => $other->id]); // not mine

        Sanctum::actingAs($me);

        $this->getJson('/api/trips')->assertOk()->assertJsonCount(2, 'data');

        $this->getJson('/api/trips?status=completed')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'completed');
    }

    /** 6. Only the owner may cancel/update a trip. */
    public function test_non_owner_cannot_cancel_or_update_someone_elses_trip(): void
    {
        $owner    = $this->passenger();
        $intruder = $this->passenger();
        $trip     = Trip::factory()->create(['passenger_id' => $owner->id]);

        Sanctum::actingAs($intruder);
        $this->deleteJson("/api/trips/{$trip->id}")->assertForbidden();
        $this->putJson("/api/trips/{$trip->id}", ['pickup_address' => 'X'])->assertForbidden();

        Sanctum::actingAs($owner);
        $this->deleteJson("/api/trips/{$trip->id}")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');
    }

    /** 7. A passenger may review a driver only after a completed trip. */
    public function test_passenger_can_review_a_driver_only_after_a_completed_trip(): void
    {
        $passenger = $this->passenger();
        $driver    = $this->driver();

        Sanctum::actingAs($passenger);
        $this->postJson("/api/reviews/{$driver->id}", ['rating' => 5])->assertForbidden();

        Trip::factory()->completed()->create([
            'passenger_id' => $passenger->id,
            'driver_id'    => $driver->id,
        ]);

        $this->postJson("/api/reviews/{$driver->id}", [
            'rating'  => 5,
            'comment' => 'Great ride',
        ])->assertCreated()->assertJsonPath('data.rating', 5);
    }
}
