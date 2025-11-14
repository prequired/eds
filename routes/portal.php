<?php

declare(strict_types=1);

use App\Livewire\Portal\ClientDashboard;
use App\Livewire\Portal\ClientInvoiceList;
use App\Livewire\Portal\ClientInvoiceView;
use App\Livewire\Portal\ClientLogin;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Client Portal Routes
|--------------------------------------------------------------------------
|
| These routes are for the client portal, where clients can view their
| invoices, make payments, and manage their profile.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('portal')->name('portal.')->group(function () {

    // Guest routes (Login)
    Route::middleware('guest:client')->group(function () {
        Route::get('/login', ClientLogin::class)->name('login');
    });

    // Authenticated client routes
    Route::middleware('auth:client')->group(function () {
        // Dashboard
        Route::get('/', ClientDashboard::class)->name('dashboard');
        Route::get('/dashboard', ClientDashboard::class)->name('dashboard.alt');

        // Invoices
        Route::get('/invoices', ClientInvoiceList::class)->name('invoices');
        Route::get('/invoices/{invoice}', ClientInvoiceView::class)->name('invoices.show');

        // Profile (placeholder)
        Route::get('/profile', function () {
            return view('portal.profile');
        })->name('profile');

        // Logout
        Route::post('/logout', function () {
            auth('client')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('portal.login');
        })->name('logout');
    });
});
