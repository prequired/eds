<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/tenant.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withSchedule(function (Schedule $schedule) {
        // Check website uptime every 5 minutes
        $schedule->command('websites:check-uptime')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // Check Lighthouse performance scores daily at 2 AM
        $schedule->command('websites:check-lighthouse')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
