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
        Schema::create('casinos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('casino_categories');
            $table->string('slug')->unique();
            $table->string('name');
            $table->integer('rtp')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('daily_withdrawal_amount', 15, 2)->default(0)->comment('WD Hari Ini');
            $table->integer('daily_withdrawal_players')->default(0)->comment('Player WD count');
            $table->timestamp('last_withdrawal_update')->nullable();
            $table->decimal('total_withdrawn', 15, 2)->default(0)->comment('Total withdrawals');
            $table->integer('rating')->default(value: 5)->comment('Casino rating 1-5');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casinos');
    }
};
