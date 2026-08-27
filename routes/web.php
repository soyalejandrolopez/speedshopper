<?php

use App\Livewire\Admin\Customers\CustomerShow;
use App\Livewire\Admin\Customers\CustomersIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Packages\PackageShow;
use App\Livewire\Admin\Packages\PackagesIndex;
use App\Livewire\Admin\Payments\PaymentsIndex;
use App\Livewire\Admin\Requests\RequestShow;
use App\Livewire\Admin\Requests\RequestsIndex;
use App\Livewire\Admin\Settings\SettingsIndex;
use App\Livewire\Admin\Shipments\ShipmentShow;
use App\Livewire\Admin\Shipments\ShipmentsIndex;
use App\Livewire\Portal\Packages\MyPackages;
use App\Livewire\Portal\Payments\MyPayments;
use App\Livewire\Portal\PortalDashboard;
use App\Livewire\Portal\Requests\MyRequests;
use App\Livewire\Portal\Requests\MyRequestShow;
use App\Livewire\Portal\Shipments\MyShipments;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::view('/solicitar', 'request')->name('request');

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en'], true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)
        ->middleware('role:admin')
        ->name('dashboard');

    Route::get('/profile', fn () => view('profile'))->name('profile');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/reports', \App\Livewire\Admin\Reports\ReportsIndex::class)->name('reports.index');

        Route::get('/customers', CustomersIndex::class)->name('customers.index');
        Route::get('/customers/{customer}', CustomerShow::class)->name('customers.show');

        Route::get('/requests', RequestsIndex::class)->name('requests.index');
        Route::get('/requests/{purchaseRequest}', RequestShow::class)->name('requests.show');
        Route::get('/requests/{purchaseRequest}/print', [\App\Http\Controllers\PrintController::class, 'requestQuote'])->name('requests.print');

        Route::get('/packages', PackagesIndex::class)->name('packages.index');
        Route::get('/packages/{package}', PackageShow::class)->name('packages.show');

        Route::get('/shipments', ShipmentsIndex::class)->name('shipments.index');
        Route::get('/shipments/{shipment}', ShipmentShow::class)->name('shipments.show');
        Route::get('/shipments/{shipment}/print', [\App\Http\Controllers\PrintController::class, 'shipmentReceipt'])->name('shipments.print');

        Route::get('/payments', PaymentsIndex::class)->name('payments.index');
        Route::get('/payments/{payment}', \App\Livewire\Admin\Payments\PaymentShow::class)->name('payments.show');

        Route::get('/settings', SettingsIndex::class)->name('settings.index');
    });

    Route::prefix('portal')->name('portal.')->middleware('role:client')->group(function () {
        Route::get('/', PortalDashboard::class)->name('dashboard');

        Route::get('/requests', MyRequests::class)->name('requests.index');
        Route::get('/requests/{purchaseRequest}', MyRequestShow::class)->name('requests.show');

        Route::get('/packages', MyPackages::class)->name('packages.index');
        Route::get('/shipments', MyShipments::class)->name('shipments.index');
        Route::get('/payments', MyPayments::class)->name('payments.index');
    });
});

require __DIR__.'/auth.php';
