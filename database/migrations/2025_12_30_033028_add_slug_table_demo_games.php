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
        Schema::table('demo_games', function (Blueprint $table) {
            $table->string('slug')->after('title')->unique();
            $table->string('url')->after('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demo_games', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('url');
        });
    }
};
