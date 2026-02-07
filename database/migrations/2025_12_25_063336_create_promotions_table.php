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
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('background_color')->default('#0f172a');
            $table->string('text_color')->default('#ffffff');
            $table->string('button_color')->default('#f59e0b');
            $table->string('button_text_color')->default('#ffffff');
            $table->string('position')->default('top'); // top, bottom, popup
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0)->unique(); // Higher priority = shown first
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
