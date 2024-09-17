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
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained("users")->cascadeOnDelete();
            $table->enum('gender', array('m', 'f', 'o'))->nullable();
            $table->float('height')->nullable();
            $table->float('start_weight')->nullable();
            $table->float('target_weight')->nullable();
            $table->integer('step_goal')->nullable();
            $table->integer('global_calorie_goal')->nullable();
            $table->integer('points_total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};
