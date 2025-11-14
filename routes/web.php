<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Application Routes
|--------------------------------------------------------------------------
|
| These routes are for the central application (Edison Tech Platform)
| Tenant routes are in routes/tenant.php
|
*/

Route::domain(config('tenancy.central_domains.0'))->group(function () {
    Route::get('/', function () {
        return view('central.pages.home');
    })->name('home');

    Route::get('/pricing', function () {
        return view('central.pages.pricing');
    })->name('pricing');

    Route::get('/features', function () {
        return view('central.pages.features');
    })->name('features');
});
