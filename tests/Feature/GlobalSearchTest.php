<?php

use App\Livewire\Admin\GlobalSearch;
use App\Models\ContactInquiry;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use Livewire\Livewire;

beforeEach(function () {
    seedRoles();
});

test('global search component renders successfully and shows quick access when query is empty', function () {
    $this->actingAs(createAdmin());

    Livewire::test(GlobalSearch::class)
        ->assertOk()
        ->assertSee(__('Quick Access'))
        ->assertSee(__('Purchase Requests'))
        ->assertSee(__('Billing & Invoices'))
        ->assertSee(__('Customers'));
});

test('global search finds customers, purchase requests, packages, shipments, payments, and inquiries', function () {
    $this->actingAs(createAdmin());

    $customer = Customer::create([
        'name' => 'Alejandro Lopez',
        'email' => 'alejandro@speedshopper.com',
        'phone' => '+18325551234',
        'address' => '123 Main St',
        'city' => 'Baytown',
        'country' => 'USA',
    ]);

    $request = PurchaseRequest::create([
        'customer_id' => $customer->id,
        'product_name' => 'Apple MacBook Pro M3',
        'store' => 'Best Buy',
        'quantity' => 1,
        'unit_price' => 1999.00,
    ]);

    $package = Package::create([
        'customer_id' => $customer->id,
        'purchase_request_id' => $request->id,
        'original_tracking' => '1Z9999999999999999',
        'store' => 'Best Buy',
        'weight_lb' => 4.5,
    ]);

    $shipment = Shipment::create([
        'customer_id' => $customer->id,
        'carrier' => 'DHL Express',
        'tracking_number' => 'DHL987654321',
        'destination_address' => 'Calle 100 #15-20',
        'destination_city' => 'Bogota',
        'destination_country' => 'Colombia',
        'shipping_cost' => 85.00,
    ]);

    $payment = Payment::create([
        'customer_id' => $customer->id,
        'reference' => 'ZELLE-998877',
        'invoice_total' => 200.00,
        'amount_paid' => 200.00,
        'paid_at' => now(),
    ]);

    $inquiry = ContactInquiry::create([
        'name' => 'Alejandro Inquiry',
        'email' => 'inquiry@speedshopper.com',
        'subject' => 'Consulta sobre tarifas especiales',
        'message' => 'Quiero cotizar un envío grande.',
    ]);

    // Test searching by customer name
    Livewire::test(GlobalSearch::class)
        ->set('query', 'Alejandro')
        ->assertSee('Alejandro Lopez')
        ->assertSee($customer->number);

    // Test searching by product name
    Livewire::test(GlobalSearch::class)
        ->set('query', 'MacBook')
        ->assertSee('Apple MacBook Pro M3')
        ->assertSee($request->number);

    // Test searching by tracking number
    Livewire::test(GlobalSearch::class)
        ->set('query', '1Z999999')
        ->assertSee($package->number)
        ->assertSee('1Z9999999999999999');

    // Test searching by carrier
    Livewire::test(GlobalSearch::class)
        ->set('query', 'DHL')
        ->assertSee($shipment->number)
        ->assertSee('DHL Express');

    // Test searching by payment reference
    Livewire::test(GlobalSearch::class)
        ->set('query', 'ZELLE-998877')
        ->assertSee($payment->number)
        ->assertSee('ZELLE-998877');

    // Test category filter
    Livewire::test(GlobalSearch::class)
        ->set('query', 'Alejandro')
        ->call('setCategory', 'customers')
        ->assertSee('Alejandro Lopez')
        ->assertDontSee('Consulta sobre tarifas especiales');
});
