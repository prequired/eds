<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained()->onDelete('cascade');

            // Project details
            $table->string('name');
            $table->text('description')->nullable();

            // Status
            $table->string('status', 20)->default('planning');
            $table->string('priority', 10)->default('medium');

            // Budget & timeline
            $table->integer('budget_cents')->nullable();
            $table->char('currency', 3)->default('usd');
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->default(0);

            $table->date('starts_at')->nullable();
            $table->date('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Settings
            $table->boolean('billable')->default(true);
            $table->integer('hourly_rate_cents')->nullable();

            // Soft delete
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('client_id');
            $table->index('status');
            $table->index('due_at');
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
