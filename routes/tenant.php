<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Actions\Logout;
use App\Livewire\Tenant\Clients\ClientForm;
use App\Livewire\Tenant\Clients\ClientList;
use App\Livewire\Tenant\Projects\ProjectForm;
use App\Livewire\Tenant\Projects\ProjectList;
use App\Livewire\Tenant\Websites\WebsiteForm;
use App\Livewire\Tenant\Websites\WebsiteList;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Landing page redirects to dashboard if authenticated
    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('login');
    });

    // Guest routes (Authentication)
    Route::middleware('guest')->group(function () {
        Volt::route('register', 'pages.auth.register')
            ->name('register');

        Volt::route('login', 'pages.auth.login')
            ->name('login');

        Volt::route('forgot-password', 'pages.auth.forgot-password')
            ->name('password.request');

        Volt::route('reset-password/{token}', 'pages.auth.reset-password')
            ->name('password.reset');
    });

    // Authenticated routes
    Route::middleware(['auth'])->group(function () {
        // Email verification
        Volt::route('verify-email', 'pages.auth.verify-email')
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Volt::route('confirm-password', 'pages.auth.confirm-password')
            ->name('password.confirm');

        // Dashboard
        Route::get('/dashboard', function () {
            return view('tenant.dashboard');
        })->middleware('verified')->name('dashboard');

        // Profile & Logout
        Route::view('profile', 'profile')
            ->name('profile');

        Route::post('logout', function (Logout $logout) {
            $logout();
            return redirect()->route('login');
        })->name('logout');

        // Clients
        Route::get('/clients', ClientList::class)->name('clients.index');
        Route::get('/clients/create', ClientForm::class)->name('clients.create');
        Route::get('/clients/{client}/edit', ClientForm::class)->name('clients.edit');

        // Projects
        Route::get('/projects', ProjectList::class)->name('projects.index');
        Route::get('/projects/create', ProjectForm::class)->name('projects.create');
        Route::get('/projects/{project}/edit', ProjectForm::class)->name('projects.edit');

        // Websites
        Route::get('/websites', WebsiteList::class)->name('websites.index');
        Route::get('/websites/create', WebsiteForm::class)->name('websites.create');
        Route::get('/websites/{website}/edit', WebsiteForm::class)->name('websites.edit');
    });
});
