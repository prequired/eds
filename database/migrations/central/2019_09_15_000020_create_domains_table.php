<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->onDelete('cascade');

            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);

            // SSL
            $table->string('certificate_status', 20)->default('pending');
            $table->timestamp('certificate_issued_at')->nullable();
            $table->timestamp('certificate_expires_at')->nullable();

            // DNS
            $table->boolean('dns_verified')->default(false);
            $table->timestamp('dns_verified_at')->nullable();
            $table->string('dns_txt_record')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
}
