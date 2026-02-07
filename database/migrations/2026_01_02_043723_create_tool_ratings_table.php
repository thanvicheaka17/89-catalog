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
        Schema::create('tool_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('tool_id')->constrained('tools')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->comment('Rating from 1 to 5');
            $table->text('review')->nullable()->comment('Optional user review');
            $table->timestamps();

            // Prevent duplicate ratings from same user on same tool
            $table->unique(['user_id', 'tool_id']);

            // Index for performance
            $table->index(['tool_id', 'rating']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_ratings');
    }
};
