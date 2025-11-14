<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Tenant\TeamInvitationSent;
use App\Events\Tenant\WebsiteUptimeChanged;
use App\Listeners\SendTeamInvitationEmail;
use App\Listeners\SendWebsiteUptimeNotifications;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register event listeners
        Event::listen(
            WebsiteUptimeChanged::class,
            SendWebsiteUptimeNotifications::class
        );

        Event::listen(
            TeamInvitationSent::class,
            SendTeamInvitationEmail::class
        );
    }
}
