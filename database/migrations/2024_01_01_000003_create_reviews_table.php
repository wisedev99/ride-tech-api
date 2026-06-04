<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('passenger_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            // driver_id is already indexed by its foreign key constraint
            $table->unique(['trip_id', 'passenger_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
