<?php

use App\Enums\PackageStatus;
use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;
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
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\Shipment;
use Livewire\Livewire;

test('admin can view dashboard', function () {
    $this->actingAs(createAdmin());

    Livewire::test(Dashboard::class)
        ->assertOk()
        ->assertSee(__('Total Customers'))
        ->assertSee(__('Balance'));
});

test('admin can manage customers', function () {
    $this->actingAs(createAdmin());

    Livewire::test(CustomersIndex::class)
        ->assertOk()
        ->call('openCreate')
        ->set('form.name', 'Nuevo Cliente')
        ->set('form.email', 'nuevo@example.com')
        ->call('save')
        ->assertHasNoErrors();

    $customer = Customer::where('email', 'nuevo@example.com')->first();
    expect($customer)->not->toBeNull()
        ->and($customer->number)->toMatch('/^CUST-\d{4}$/');
});

test('admin can create purchase request with cost items', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();

    Livewire::test(RequestsIndex::class)
        ->call('openCreate')
        ->set('form.customer_id', $customer->id)
        ->set('form.product_name', 'Nike Shoes')
        ->set('form.product_url', 'https://nike.com/shoes')
        ->set('form.quantity', 2)
        ->call('save')
        ->assertHasNoErrors();

    $request = PurchaseRequest::first();
    expect($request)->not->toBeNull()
        ->and($request->status)->toBe(RequestStatus::New)
        ->and($request->number)->toMatch('/^REQ-\d{4}$/');
});

test('admin can create package with photo validation', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();

    Livewire::test(PackagesIndex::class)
        ->call('openCreate')
        ->set('form.customer_id', $customer->id)
        ->set('form.store', 'Amazon')
        ->set('form.weight_lb', 4.5)
        ->call('save')
        ->assertHasNoErrors();

    $package = Package::first();
    expect($package->number)->toMatch('/^PKG-\d{4}$/');
});

test('admin can create shipment consolidating packages', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $p1 = Package::factory()->create(['customer_id' => $customer->id]);
    $p2 = Package::factory()->create(['customer_id' => $customer->id]);

    Livewire::test(ShipmentsIndex::class)
        ->call('openCreate')
        ->set('form.customer_id', $customer->id)
        ->set('form.carrier', 'DHL Express')
        ->set('form.package_ids', [$p1->id, $p2->id])
        ->call('save')
        ->assertHasNoErrors();

    $shipment = Shipment::first();
    expect($shipment->number)->toMatch('/^BOX-\d{4}$/')
        ->and($shipment->packages()->count())->toBe(2)
        ->and($p1->refresh()->status)->toBe(PackageStatus::Ready);
});

test('admin can register payments', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();

    Livewire::test(PaymentsIndex::class)
        ->call('openCreate')
        ->set('form.customer_id', $customer->id)
        ->set('form.invoice_total', 150.00)
        ->set('form.amount_paid', 100.00)
        ->set('form.payment_method', 'zelle')
        ->call('save')
        ->assertHasNoErrors();

    $payment = Payment::first();
    expect($payment->balance_due)->toBe(50.00)
        ->and($payment->number)->toMatch('/^PAY-\d{4}$/');
});

test('admin can transition request status with history', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    Livewire::test(RequestShow::class, ['purchaseRequest' => $request])
        ->set('newStatus', RequestStatus::Quoted->value)
        ->set('transitionNote', 'Cotización enviada por WhatsApp')
        ->call('transitionStatus')
        ->assertHasNoErrors();

    $request->refresh();
    expect($request->status)->toBe(RequestStatus::Quoted)
        ->and($request->statusHistory()->count())->toBe(1)
        ->and($request->statusHistory()->first()->from)->toBe(RequestStatus::New->value);
});

test('admin can transition a cancelled request back to an active status', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'status' => RequestStatus::Cancelled->value,
    ]);

    Livewire::test(RequestShow::class, ['purchaseRequest' => $request])
        ->set('newStatus', RequestStatus::Quoted->value)
        ->set('transitionNote', 'Reabierto a solicitud del cliente')
        ->call('transitionStatus')
        ->assertHasNoErrors();

    $request->refresh();
    expect($request->status)->toBe(RequestStatus::Quoted)
        ->and($request->statusHistory()->count())->toBe(1)
        ->and($request->statusHistory()->first()->from)->toBe(RequestStatus::Cancelled->value);
});

test('admin can transition shipment and packages follow', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $package = Package::factory()->create(['customer_id' => $customer->id, 'status' => 'ready']);
    $shipment = Shipment::factory()->create(['customer_id' => $customer->id, 'status' => 'ready']);
    $shipment->packages()->attach($package);

    Livewire::test(ShipmentShow::class, ['shipment' => $shipment])
        ->set('newStatus', ShipmentStatus::InTransit->value)
        ->call('transitionStatus')
        ->assertHasNoErrors();

    expect($shipment->refresh()->status)->toBe(ShipmentStatus::InTransit)
        ->and($package->refresh()->status)->toBe(PackageStatus::Shipped);
});

test('advancing a request through the unified flow syncs packages and shipments', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'status' => RequestStatus::Received->value,
    ]);
    $package = Package::factory()->create([
        'customer_id' => $customer->id,
        'purchase_request_id' => $request->id,
        'status' => PackageStatus::Storing->value,
    ]);
    $shipment = Shipment::factory()->create(['customer_id' => $customer->id, 'status' => ShipmentStatus::Ready->value]);
    $shipment->packages()->attach($package);

    foreach ([
        RequestStatus::Packing->value => PackageStatus::Packing,
        RequestStatus::Ready->value => PackageStatus::Ready,
        RequestStatus::Shipped->value => PackageStatus::Shipped,
    ] as $next => $packageStatus) {
        Livewire::test(RequestShow::class, ['purchaseRequest' => $request->fresh()])
            ->set('newStatus', $next)
            ->call('transitionStatus')
            ->assertHasNoErrors();
    }

    expect($request->refresh()->status)->toBe(RequestStatus::Shipped)
        ->and($package->refresh()->status)->toBe(PackageStatus::Shipped)
        ->and($shipment->refresh()->status)->toBe(ShipmentStatus::InTransit)
        ->and($shipment->refresh()->shipped_at)->not->toBeNull();

    Livewire::test(RequestShow::class, ['purchaseRequest' => $request->fresh()])
        ->set('newStatus', RequestStatus::Delivered->value)
        ->call('transitionStatus')
        ->assertHasNoErrors();

    expect($request->refresh()->status)->toBe(RequestStatus::Delivered)
        ->and($package->fresh()->status)->toBe(PackageStatus::Delivered)
        ->and($shipment->fresh()->status)->toBe(ShipmentStatus::Delivered)
        ->and($shipment->fresh()->delivered_at)->not->toBeNull();
});

test('admin can update settings', function () {
    $this->actingAs(createAdmin());

    Livewire::test(SettingsIndex::class)
        ->set('settings.shopper_fee', '12.50')
        ->set('settings.company_name', 'Mi Empresa')
        ->call('save')
        ->assertHasNoErrors();

    expect(Setting::get('shopper_fee'))->toBe('12.50')
        ->and(Setting::get('company_name'))->toBe('Mi Empresa');
});

test('customer show page renders for admin', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();

    Livewire::test(CustomerShow::class, ['customer' => $customer])
        ->assertOk()
        ->assertSee($customer->number)
        ->assertSee(__('Customer Details'));
});

test('package show page renders for admin', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $package = Package::factory()->create(['customer_id' => $customer->id]);

    Livewire::test(PackageShow::class, ['package' => $package])
        ->assertOk()
        ->assertSee($package->status->label())
        ->assertSee(__('Status History'));
});
