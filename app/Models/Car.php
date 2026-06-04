<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'brand',
        'model',
        'plate_number',
    ];

    /**
     * The driver (user) that owns the car.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
