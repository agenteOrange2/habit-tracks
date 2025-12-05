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
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('difficulty')->default('medium');
            
            // Recurrencia
            $table->string('frequency')->default('daily');
            $table->json('schedule')->nullable();
            $table->boolean('is_recurring')->default(true);
            
            // Gamificación
            $table->integer('points_reward')->default(10);
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            
            // Pomodoro
            $table->integer('estimated_pomodoros')->nullable();
            
            // Personalización
            $table->string('color')->default('#3B82F6');
            $table->string('icon')->default('📝');
            
            // Recordatorios
            $table->boolean('reminder_enabled')->default(false);
            $table->time('reminder_time')->nullable();
            
            // Estado
            $table->boolean('is_active')->default(true);
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
