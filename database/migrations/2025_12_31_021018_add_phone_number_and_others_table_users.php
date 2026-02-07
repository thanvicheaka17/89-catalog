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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable();
            $table->string('country_code')->nullable();
            $table->string('location')->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->unsignedInteger('login_count')->default(0);
            $table->boolean('two_factor_enabled')->default(false);
            $table->boolean('login_notifications')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->string('language')->default('en');
            $table->string('timezone')->default('GMT+7');
            $table->string('two_factor_secret')->nullable();
            $table->timestamp('two_factor_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('country_code');
            $table->dropColumn('location');
            $table->dropColumn('birth_date');
            $table->dropColumn('last_login_at');
            $table->dropColumn('login_count');
            $table->dropColumn('two_factor_enabled');
            $table->dropColumn('login_notifications');
            $table->dropColumn('email_notifications');
            $table->dropColumn('sms_notifications');
            $table->dropColumn('push_notifications');
            $table->dropColumn('language');
            $table->dropColumn('timezone');
            $table->dropColumn('two_factor_secret');
            $table->dropColumn('two_factor_expires_at');
        });
    }
};
