<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Basic info
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('website')->nullable();

            // Address
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->char('country', 2)->default('US');

            // Billing
            $table->string('billing_email')->nullable();
            $table->integer('monthly_retainer_cents')->default(0);
            $table->char('currency', 3)->default('usd');
            $table->integer('payment_terms')->default(30);

            // Status
            $table->string('status', 20)->default('active');

            // Settings & metadata
            $table->jsonb('settings')->default('{}');
            $table->text('notes')->nullable();
            $table->jsonb('tags')->default('[]');

            // Soft delete
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('email');
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
