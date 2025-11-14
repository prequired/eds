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
        Schema::table('websites', function (Blueprint $table) {
            $table->json('notification_emails')->nullable()->after('notes');
            $table->boolean('notify_on_downtime')->default(true)->after('notification_emails');
            $table->boolean('notify_on_recovery')->default(true)->after('notify_on_downtime');
            $table->timestamp('last_notified_at')->nullable()->after('notify_on_recovery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn([
                'notification_emails',
                'notify_on_downtime',
                'notify_on_recovery',
                'last_notified_at',
            ]);
        });
    }
};
