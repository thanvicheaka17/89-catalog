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
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('background_color_2', 7)->nullable()->after('background_color');
            $table->string('background_gradient_type', 20)->default('solid')->after('background_color_2');
            $table->string('background_gradient_direction', 30)->nullable()->after('background_gradient_type');
            
            $table->string('button_color_2', 7)->nullable()->after('button_color');
            $table->string('button_gradient_type', 20)->default('solid')->after('button_color_2');
            $table->string('button_gradient_direction', 30)->nullable()->after('button_gradient_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn([
                'background_color_2',
                'background_gradient_type',
                'background_gradient_direction',
                'button_color_2',
                'button_gradient_type',
                'button_gradient_direction',
            ]);
        });
    }
};
