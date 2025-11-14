<?php

declare(strict_types=1);

use App\Livewire\Tenant\Clients\ClientForm;
use App\Livewire\Tenant\Clients\ClientList;
use App\Livewire\Tenant\Projects\ProjectForm;
use App\Livewire\Tenant\Projects\ProjectList;
use Illuminate\Support\Facades\Route;
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

    Route::get('/', function () {
        return view('tenant.dashboard');
    })->middleware(['auth'])->name('tenant.dashboard');

    // Authenticated routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('tenant.dashboard');
        })->name('dashboard');

        // Clients
        Route::get('/clients', ClientList::class)->name('clients.index');
        Route::get('/clients/create', ClientForm::class)->name('clients.create');
        Route::get('/clients/{client}/edit', ClientForm::class)->name('clients.edit');

        // Projects
        Route::get('/projects', ProjectList::class)->name('projects.index');
        Route::get('/projects/create', ProjectForm::class)->name('projects.create');
        Route::get('/projects/{project}/edit', ProjectForm::class)->name('projects.edit');

        // Websites
        Route::get('/websites', function () {
            return view('tenant.websites.index');
        })->name('websites.index');
    });
});
