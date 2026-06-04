<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'passenger_id',
        'trip_id',
        'rating',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * The driver (user) being reviewed.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * The passenger (user) who wrote the review.
     */
    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    /**
     * The trip this review belongs to.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }
}
