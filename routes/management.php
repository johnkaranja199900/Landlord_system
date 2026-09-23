<?php

use App\Http\Controllers\Admin\LandlordController as AdminLandlordController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\LedgerController;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Finance\ReportController;
use App\Http\Controllers\Property\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenancyController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated application routes
|
| Every sensitive action is authorized SERVER SIDE through:
|   1. the `permission` / `role` middleware on routes & controllers, and
|   2. policies ($this->authorize(...)) inside controller actions.
| Hiding buttons in Blade is only cosmetic — never the actual defense.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* Global configuration — Super Administrator (roles.manage permission) */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('roles', [RoleManagementController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleManagementController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [RoleManagementController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleManagementController::class, 'destroy'])->name('roles.destroy');
        Route::get('permissions', [RoleManagementController::class, 'permissions'])->name('permissions');
        Route::put('users/{user}/permissions', [RoleManagementController::class, 'userPermissions'])->name('users.permissions');
        Route::post('users/{user}/roles', [RoleManagementController::class, 'assignRole'])->name('users.roles.assign');
        Route::delete('users/{user}/roles/{role}', [RoleManagementController::class, 'removeRole'])->name('users.roles.remove');

        Route::get('landlords', [AdminLandlordController::class, 'index'])->name('landlords.index');
        Route::post('landlords', [AdminLandlordController::class, 'store'])->name('landlords.store');
        Route::put('landlords/{landlord}', [AdminLandlordController::class, 'update'])->name('landlords.update');
    });

    /* Properties */
    Route::resource('properties', PropertyController::class)->except(['edit']);

    /* Blocks */
    Route::get('properties/{property}/blocks', [BlockController::class, 'index'])->name('blocks.index');
    Route::post('properties/{property}/blocks', [BlockController::class, 'store'])->name('blocks.store');
    Route::get('blocks/{block}', [BlockController::class, 'show'])->name('blocks.show');
    Route::put('blocks/{block}', [BlockController::class, 'update'])->name('blocks.update');
    Route::delete('blocks/{block}', [BlockController::class, 'destroy'])->name('blocks.destroy');

    /* Units */
    Route::get('blocks/{block}/units', [UnitController::class, 'index'])->name('units.index');
    Route::post('blocks/{block}/units', [UnitController::class, 'store'])->name('units.store');
    Route::get('units/{unit}', [UnitController::class, 'show'])->name('units.show');
    Route::put('units/{unit}', [UnitController::class, 'update'])->name('units.update');
    Route::patch('units/{unit}/rent', [UnitController::class, 'updateRent'])->name('units.rent');
    Route::get('units/{unit}/rent-history', [UnitController::class, 'rentHistory'])->name('units.rent-history');
    Route::delete('units/{unit}', [UnitController::class, 'destroy'])->name('units.destroy');

    /* Tenants */
    Route::resource('tenants', TenantController::class);

    /* Tenancies / allocation */
    Route::get('tenancies/allocate', [TenancyController::class, 'create'])->name('tenancies.create');
    Route::get('tenancies', [TenancyController::class, 'index'])->name('tenancies.index');
    Route::post('tenancies', [TenancyController::class, 'store'])->name('tenancies.store');
    Route::get('tenancies/{tenancy}', [TenancyController::class, 'show'])->name('tenancies.show');
    Route::post('tenancies/{tenancy}/terminate', [TenancyController::class, 'terminate'])->name('tenancies.terminate');
    Route::patch('tenancies/{tenancy}/status', [TenancyController::class, 'updateStatus'])->name('tenancies.status');

    /* Finance: invoices, payments/receipts, rent ledger, reports */
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show', 'create', 'store']);
    Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');

    Route::resource('payments', PaymentController::class)->only(['index', 'show', 'create', 'store']);
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::post('payments/{payment}/reverse', [PaymentController::class, 'reverse'])->name('payments.reverse');

    Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('tenancies/{tenancy}/ledger', [LedgerController::class, 'tenantLedger'])->name('ledger.tenant');
    Route::post('tenancies/{tenancy}/ledger/adjust', [LedgerController::class, 'adjust'])->name('ledger.adjust');

    Route::get('reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
});
