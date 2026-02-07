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
        Schema::create('level_configurations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('level')->unique();
            $table->decimal('threshold', 15, 2);
            $table->string('tier');
            $table->string('tier_name'); // Bronze, Silver, Gold, Platinum, Diamond
            $table->json('tier_info'); // Store tier details as JSON
            $table->integer('tier_min_level');
            $table->integer('tier_max_level');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tier', 'level']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_configurations');
    }
};
