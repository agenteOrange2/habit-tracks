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
        Schema::create('user_pomodoro_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('short_break_duration')->default(5); // minutes
            $table->integer('long_break_duration')->default(15); // minutes
            $table->boolean('auto_start_breaks')->default(true);
            $table->boolean('sound_enabled')->default(true);
            $table->integer('cycle_count')->default(0); // 0-4, tracks Pomodoros in current cycle
            $table->timestamps();

            // Ensure one settings record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_pomodoro_settings');
    }
};
