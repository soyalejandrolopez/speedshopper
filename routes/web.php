<?php

use App\Http\Controllers\PricingPdfController;
use App\Http\Controllers\PrintController;
use App\Livewire\Admin\Billing\BillingIndex;
use App\Livewire\Admin\Customers\CustomerShow;
use App\Livewire\Admin\Customers\CustomersIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Inquiries\InquiriesIndex;
use App\Livewire\Admin\Mail\MailCompose;
use App\Livewire\Admin\Packages\PackageShow;
use App\Livewire\Admin\Packages\PackagesIndex;
use App\Livewire\Admin\Payments\PaymentShow;
use App\Livewire\Admin\Payments\PaymentsIndex;
use App\Livewire\Admin\Rates\RatesIndex;
use App\Livewire\Admin\Reports\ReportsIndex;
use App\Livewire\Admin\Requests\RequestShow;
use App\Livewire\Admin\Requests\RequestsIndex;
use App\Livewire\Admin\Settings\SettingsIndex;
use App\Livewire\Admin\Shipments\ShipmentShow;
use App\Livewire\Admin\Shipments\ShipmentsIndex;
use App\Livewire\Portal\Billing\PortalBillingIndex;
use App\Livewire\Portal\Packages\MyPackages;
use App\Livewire\Portal\Payments\MyPayments;
use App\Livewire\Portal\PortalDashboard;
use App\Livewire\Portal\Requests\CreateRequest;
use App\Livewire\Portal\Requests\MyRequests;
use App\Livewire\Portal\Requests\MyRequestShow;
use App\Livewire\Portal\Shipments\MyShipments;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

Route::view('/solicitar', 'request')->name('request');
Route::view('/contacto', 'contact')->name('contact');
Route::view('/contact', 'contact');
Route::view('/productos-prohibidos', 'prohibited-items')->name('prohibited-items');
Route::view('/prohibidos-y-restringidos', 'prohibited-items');
Route::view('/prohibited-items', 'prohibited-items');

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

    Route::get('/requests/{purchaseRequest}/print', [PrintController::class, 'requestQuote'])->name('requests.print');
    Route::get('/shipments/{shipment}/print', [PrintController::class, 'shipmentReceipt'])->name('shipments.print');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/requests/{purchaseRequest}/print', [PrintController::class, 'requestQuote'])->name('requests.print');
        Route::get('/shipments/{shipment}/print', [PrintController::class, 'shipmentReceipt'])->name('shipments.print');
        Route::get('/reports', ReportsIndex::class)->name('reports.index');

        Route::get('/customers', CustomersIndex::class)->name('customers.index');
        Route::get('/customers/{customer}', CustomerShow::class)->name('customers.show');

        Route::get('/requests', RequestsIndex::class)->name('requests.index');
        Route::get('/requests/{purchaseRequest}', RequestShow::class)->name('requests.show');

        Route::get('/packages', PackagesIndex::class)->name('packages.index');
        Route::get('/packages/{package}', PackageShow::class)->name('packages.show');

        Route::get('/shipments', ShipmentsIndex::class)->name('shipments.index');
        Route::get('/shipments/{shipment}', ShipmentShow::class)->name('shipments.show');

        Route::get('/payments', PaymentsIndex::class)->name('payments.index');
        Route::get('/payments/{payment}', PaymentShow::class)->name('payments.show');

        Route::get('/facturacion', BillingIndex::class)->name('billing.index');
        Route::get('/tarifario', RatesIndex::class)->name('rates.index');

        Route::get('/inquiries', InquiriesIndex::class)->name('inquiries.index');

        Route::get('/mail', MailCompose::class)->name('mail.index');

        Route::get('/rates/pdf', [PricingPdfController::class, 'download'])->name('rates.pdf');

        Route::get('/settings', SettingsIndex::class)->name('settings.index');
    });

    Route::prefix('portal')->name('portal.')->middleware('role:client')->group(function () {
        Route::get('/', PortalDashboard::class)->name('dashboard');

        Route::get('/requests', MyRequests::class)->name('requests.index');
        Route::get('/requests/create', CreateRequest::class)->name('requests.create');
        Route::get('/requests/{purchaseRequest}', MyRequestShow::class)->name('requests.show');
        Route::get('/requests/{purchaseRequest}/print', [PrintController::class, 'requestQuote'])->name('requests.print');

        Route::get('/packages', MyPackages::class)->name('packages.index');
        Route::get('/shipments', MyShipments::class)->name('shipments.index');
        Route::get('/shipments/{shipment}/print', [PrintController::class, 'shipmentReceipt'])->name('shipments.print');
        Route::get('/payments', MyPayments::class)->name('payments.index');
        Route::get('/facturacion', PortalBillingIndex::class)->name('billing.index');
    });
});

require __DIR__.'/auth.php';
