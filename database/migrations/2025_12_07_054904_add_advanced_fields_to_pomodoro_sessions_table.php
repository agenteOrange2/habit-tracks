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
        Schema::table('pomodoro_sessions', function (Blueprint $table) {
            // Session type: 'pomodoro', 'short_break', 'long_break'
            $table->string('session_type', 20)->default('pomodoro')->after('duration_minutes');
            
            // Link to original session if this is a resumed session
            $table->foreignId('resumed_from_id')->nullable()->after('session_type')
                ->constrained('pomodoro_sessions')->nullOnDelete();
            
            // Store remaining seconds when interrupted for resume functionality
            $table->integer('remaining_seconds')->nullable()->after('resumed_from_id');
            
            // Add index for filtering by session type
            $table->index('session_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pomodoro_sessions', function (Blueprint $table) {
            $table->dropIndex(['session_type']);
            $table->dropForeign(['resumed_from_id']);
            $table->dropColumn(['session_type', 'resumed_from_id', 'remaining_seconds']);
        });
    }
};
