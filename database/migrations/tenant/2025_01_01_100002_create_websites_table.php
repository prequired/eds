<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('project_id')->nullable()->constrained()->onDelete('set null');

            // Website details
            $table->string('name');
            $table->string('url');
            $table->string('environment', 20)->default('production'); // production, staging, development
            $table->string('status', 20)->default('active');

            // Server details
            $table->string('server_provider', 50)->nullable(); // digitalocean, aws, etc
            $table->string('server_id')->nullable();
            $table->string('server_ip')->nullable();

            // Repository
            $table->string('repository_provider', 50)->nullable(); // github, gitlab, bitbucket
            $table->string('repository_url')->nullable();
            $table->string('repository_branch', 100)->default('main');

            // Deployment
            $table->string('deployment_method', 50)->nullable(); // forge, github_actions, manual
            $table->string('deployment_status', 20)->default('idle');
            $table->timestamp('last_deployed_at')->nullable();

            // Monitoring
            $table->string('uptime_status', 20)->default('unknown'); // up, down, unknown
            $table->timestamp('last_checked_at')->nullable();
            $table->integer('response_time_ms')->nullable();

            // Performance
            $table->integer('lighthouse_performance')->nullable();
            $table->integer('lighthouse_accessibility')->nullable();
            $table->integer('lighthouse_seo')->nullable();
            $table->timestamp('lighthouse_checked_at')->nullable();

            // Settings
            $table->jsonb('settings')->default('{}');
            $table->text('notes')->nullable();

            // Soft delete
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('client_id');
            $table->index('project_id');
            $table->index('status');
            $table->index('uptime_status');
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
