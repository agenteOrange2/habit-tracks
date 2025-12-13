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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_seed')->nullable()->after('email'); // DiceBear seed
            $table->string('avatar_style')->default('notionists')->after('avatar_seed'); // DiceBear style
            $table->string('player_class')->default('programador')->after('avatar_style'); // Player class/role
            $table->string('cover_image')->nullable()->after('player_class'); // Cover image URL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_seed', 'avatar_style', 'player_class', 'cover_image']);
        });
    }
};
