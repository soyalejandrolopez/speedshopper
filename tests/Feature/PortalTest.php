<?php

use App\Livewire\Portal\Packages\MyPackages;
use App\Livewire\Portal\Payments\MyPayments;
use App\Livewire\Portal\PortalDashboard;
use App\Livewire\Portal\Requests\MyRequests;
use App\Livewire\Portal\Requests\MyRequestShow;
use App\Livewire\Portal\Shipments\MyShipments;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use Livewire\Livewire;

test('client can view portal dashboard', function () {
    $client = createClient();

    Livewire::actingAs($client)->test(PortalDashboard::class)
        ->assertOk()
        ->assertSee(__('Hello'));
});

test('client can create a purchase request from portal', function () {
    $client = createClient();

    Livewire::actingAs($client)->test(MyRequests::class)
        ->call('openForm')
        ->set('form.product_name', 'Adidas Samba')
        ->set('form.product_url', 'https://adidas.com/samba')
        ->set('form.quantity', 1)
        ->call('save')
        ->assertHasNoErrors();

    $request = PurchaseRequest::first();
    expect($request->customer_id)->toBe($client->customer->id);
});

test('client can see own request details', function () {
    $client = createClient();
    $request = PurchaseRequest::factory()->create(['customer_id' => $client->customer->id]);

    Livewire::actingAs($client)->test(MyRequestShow::class, ['purchaseRequest' => $request])
        ->assertOk()
        ->assertSee($request->product_name);
});

test('client cannot see another customers request details', function () {
    $client = createClient();
    $other = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $other->id]);

    Livewire::actingAs($client)->test(MyRequestShow::class, ['purchaseRequest' => $request])
        ->assertForbidden();
});

test('client can view packages, shipments and payments lists', function () {
    $client = createClient();

    Livewire::actingAs($client)->test(MyPackages::class)->assertOk();
    Livewire::actingAs($client)->test(MyShipments::class)->assertOk();
    Livewire::actingAs($client)->test(MyPayments::class)->assertOk();
});

test('client can create a purchase request via dedicated create form', function () {
    $client = createClient();

    Livewire::actingAs($client)->test(\App\Livewire\Portal\Requests\CreateRequest::class)
        ->assertOk()
        ->set('form.product_name', 'Nike Air Max')
        ->set('form.product_url', 'https://nike.com/airmax')
        ->set('form.quantity', 2)
        ->set('form.unit_price', 120.00)
        ->set('form.services', ['personal_shopper'])
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect();

    $request = PurchaseRequest::where('product_name', 'Nike Air Max')->first();
    expect($request)->not->toBeNull()
        ->and($request->customer_id)->toBe($client->customer->id);
});
