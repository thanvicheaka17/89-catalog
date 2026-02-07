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
        Schema::create('hot_and_fresh', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();

            $table->decimal('rating', 3, 1)->default(0.0); // e.g., 4.9
            $table->integer('user_count')->default(0);    // e.g., 12000
            $table->integer('active_hours')->default(0);  // e.g., 25000 hrs
            $table->integer('rank')->nullable();          // e.g., Top #1
            $table->string('badge')->nullable();         // e.g., 'Best Use', 'Premium'
            $table->string('tier')->default('Silver');   // Silver, Gold, etc.
            
            // Pricing
            $table->decimal('price', 15, 2); // IDR 40,000
            $table->integer('win_rate_increase')->nullable(); // e.g., +78%

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hot_and_fresh');
    }
};
