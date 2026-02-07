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
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('achievement_code');

            $table->string('title');
            $table->text('description')->nullable();

            $table->timestamp('unlocked_at')->nullable();

            $table->timestamps();

            // Ensure a user cannot unlock the same achievement twice
            $table->unique(['user_id', 'achievement_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};
