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
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("users")->cascadeOnDelete();
            $table->date('date');
            $table->float('weight')->nullable();
            $table->integer('training_duration'); // in minutes, 2 points per minute
            $table->integer('day_calorie_goal'); // calorie goal for the day (use global user goal if null)
            $table->decimal('percentage_of_goal', 5, 4); // percentage of the goal reached (0-1)
            $table->integer('calories');

            $table->float('water'); // water in liters (e.g. 2.5)
            /*
             * 3l -> 3pts
             * 2l -> 2pts
             * 1.5l -> 1pt
             * <1l -> -1pt
             */

            $table->float('steps'); // daily walking in km, 1 point for each 10km
            $table->integer('meals_warm'); // 2pts for each meal
            $table->integer('meals_cold'); // 1pt for each meal
            $table->boolean('is_cheat_day')->default(false); // if true, no negative points are given
            $table->boolean('took_alcohol')->default(false); // if true, -5 points
            $table->boolean('took_fast_food')->default(false); // if true, -2 points
            $table->boolean('took_sweets')->default(false); // if true, -1 point
            $table->integer('points'); // total points for the day
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
