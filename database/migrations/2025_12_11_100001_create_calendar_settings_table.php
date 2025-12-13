<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('default_duration')->default(60); // minutes
            $table->time('working_hours_start')->default('09:00');
            $table->time('working_hours_end')->default('18:00');
            $table->boolean('auto_sync')->default(false);
            $table->string('default_view')->default('month'); // month, week, day
            $table->integer('default_reminder')->default(15); // minutes
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_settings');
    }
};
