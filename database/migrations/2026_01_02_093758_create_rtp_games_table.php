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
        Schema::create('rtp_games', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('provider_id');
            $table->string('name');
            $table->unsignedTinyInteger('rtp');
            $table->unsignedTinyInteger('pola');
            $table->unsignedTinyInteger('rating')->comment('Rating from 1 to 5');
            $table->string('img_src')->nullable();
            $table->integer('step_one');
            $table->string('type_step_one')->nullable();
            $table->string('desc_step_one')->nullable();
            $table->integer('step_two');
            $table->string('type_step_two')->nullable();
            $table->string('desc_step_two')->nullable();
            $table->integer('step_three');
            $table->string('type_step_three')->nullable();
            $table->string('desc_step_three')->nullable();
            $table->integer('step_four');
            $table->string('type_step_four')->nullable();
            $table->string('desc_step_four')->nullable();
            $table->integer('stake_bet');
            $table->timestamp('last_rtp_update')->nullable();
            $table->timestamps();

            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');

            $table->index('last_rtp_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rtp_games');
    }
};
