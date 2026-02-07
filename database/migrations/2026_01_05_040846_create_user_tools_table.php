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
        Schema::create('user_tools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('tool_id')->constrained('tools')->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'expired', 'suspended'])->default('active');
            $table->timestamp('purchased_at');
            $table->timestamp('expires_at')->nullable();
            $table->integer('usage_count')->default(0);
            $table->integer('max_usage')->nullable();
            $table->decimal('price_paid', 10, 2)->default(0);
            $table->string('transaction_id')->nullable();
            $table->json('metadata')->nullable(); // For additional data like purchase source, etc.
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['tool_id']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tools');
    }
};
