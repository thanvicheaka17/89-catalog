<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_games', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Game info
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_demo')->default(true);

            // Created by user (UUID FK)
            $table->foreignUuid('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_games');
    }
};
