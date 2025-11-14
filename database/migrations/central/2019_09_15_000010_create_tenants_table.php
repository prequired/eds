<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Basic info
            $table->string('company_name');
            $table->string('subdomain', 63)->unique();
            $table->string('owner_email');

            // Subscription
            $table->string('plan', 20)->default('starter');
            $table->string('status', 20)->default('trial');
            $table->timestamp('trial_ends_at')->nullable();

            // Limits
            $table->integer('client_limit')->nullable();
            $table->integer('website_limit')->nullable();
            $table->integer('storage_limit_gb')->default(10);
            $table->integer('bandwidth_limit_gb')->default(500);

            // JSON columns
            $table->jsonb('settings')->default('{}');
            $table->jsonb('data')->default('{}');

            $table->timestamps();

            // Indexes
            $table->index('subdomain');
            $table->index('status');
            $table->index('plan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
