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
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('portal_enabled')->default(true)->after('status');
            $table->string('portal_password')->nullable()->after('portal_enabled');
            $table->timestamp('last_portal_login')->nullable()->after('portal_password');
            $table->rememberToken()->after('last_portal_login'); // For "remember me"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['portal_enabled', 'portal_password', 'last_portal_login', 'remember_token']);
        });
    }
};
