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
        Schema::create('uptime_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('website_id')->constrained('websites')->cascadeOnDelete();
            $table->string('status', 20); // up, down, unknown
            $table->integer('response_time_ms')->nullable();
            $table->integer('status_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            // Indexes for efficient queries
            $table->index('website_id');
            $table->index('checked_at');
            $table->index(['website_id', 'checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uptime_checks');
    }
};
