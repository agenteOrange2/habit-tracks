<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('habit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('recurrence_type')->nullable(); // daily, weekly, monthly
            $table->json('recurrence_days')->nullable(); // [1,3,5] for Mon,Wed,Fri
            $table->date('recurrence_end')->nullable();
            $table->foreignId('parent_event_id')->nullable();
            $table->string('google_event_id')->nullable();
            $table->boolean('sync_to_google')->default(false);
            $table->integer('reminder_minutes')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'start_time']);
            $table->index('google_event_id');
            $table->foreign('parent_event_id')->references('id')->on('calendar_events')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
