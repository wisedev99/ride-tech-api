<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Cars owned by this user (when acting as a driver).
     */
    public function cars()
    {
        return $this->hasMany(Car::class, 'driver_id');
    }

    /**
     * Trips requested by this user (as a passenger).
     */
    public function trips()
    {
        return $this->hasMany(Trip::class, 'passenger_id');
    }

    /**
     * Trips this user is driving (as a driver).
     */
    public function driverTrips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    /**
     * Reviews received by this user as a driver.
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'driver_id');
    }

    /**
     * Reviews written by this user as a passenger.
     */
    public function reviewsWritten()
    {
        return $this->hasMany(Review::class, 'passenger_id');
    }
}
