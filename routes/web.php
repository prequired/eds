<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Application Routes
|--------------------------------------------------------------------------
|
| These routes are for the central application (main landing page, etc.)
| Tenant-specific routes are defined in routes/tenant.php
|
*/

Route::view('/', 'welcome');
