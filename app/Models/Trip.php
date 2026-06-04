<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'passenger_id',
        'driver_id',
        'pickup_address',
        'destination_address',
        'preferences',
        'status',
        'accepted_at',
        'completed_at',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * The passenger (user) who requested the trip.
     */
    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    /**
     * The driver (user) assigned to the trip.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Reviews left for this trip.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'trip_id');
    }
}
