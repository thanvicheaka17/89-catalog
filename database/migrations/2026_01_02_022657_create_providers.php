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
        Schema::create('providers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('min_rtp')->default(50);
            $table->unsignedTinyInteger('max_rtp')->default(95);
            $table->unsignedTinyInteger('min_pola')->default(50);
            $table->unsignedTinyInteger('max_pola')->default(95);
            $table->string('rtp_promax_name')->nullable();
            $table->string('rtp_promax_plus_name')->nullable();
            $table->boolean('is_rtp_promax')->default(false);
            $table->boolean('is_rtp_promax_plus')->default(false);
            $table->string('rtp_promax_logo')->nullable();
            $table->string('rtp_promax_plus_logo')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
