<?php

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;

test('client can only see their own requests', function () {
    $client = createClient();
    $ownCustomer = $client->customer;
    $otherCustomer = Customer::factory()->create();

    $own = PurchaseRequest::factory()->create(['customer_id' => $ownCustomer->id]);
    $other = PurchaseRequest::factory()->create(['customer_id' => $otherCustomer->id]);

    expect($client->can('view', $own))->toBeTrue()
        ->and($client->can('view', $other))->toBeFalse()
        ->and($client->can('update', $own))->toBeFalse()
        ->and($client->can('delete', $own))->toBeFalse()
        ->and($client->can('viewAny', PurchaseRequest::class))->toBeTrue();
});

test('client can only see their own packages and shipments', function () {
    $client = createClient();
    $ownCustomer = $client->customer;
    $otherCustomer = Customer::factory()->create();

    $ownPackage = Package::factory()->create(['customer_id' => $ownCustomer->id]);
    $otherPackage = Package::factory()->create(['customer_id' => $otherCustomer->id]);
    $ownShipment = Shipment::factory()->create(['customer_id' => $ownCustomer->id]);
    $otherShipment = Shipment::factory()->create(['customer_id' => $otherCustomer->id]);

    expect($client->can('view', $ownPackage))->toBeTrue()
        ->and($client->can('view', $otherPackage))->toBeFalse()
        ->and($client->can('view', $ownShipment))->toBeTrue()
        ->and($client->can('view', $otherShipment))->toBeFalse()
        ->and($client->can('create', Package::class))->toBeFalse()
        ->and($client->can('create', Shipment::class))->toBeFalse();
});

test('client can only see their own payments and customers', function () {
    $client = createClient();
    $ownCustomer = $client->customer;
    $otherCustomer = Customer::factory()->create();

    $ownPayment = Payment::factory()->create(['customer_id' => $ownCustomer->id]);
    $otherPayment = Payment::factory()->create(['customer_id' => $otherCustomer->id]);

    expect($client->can('view', $ownPayment))->toBeTrue()
        ->and($client->can('view', $otherPayment))->toBeFalse()
        ->and($client->can('view', $ownCustomer))->toBeTrue()
        ->and($client->can('view', $otherCustomer))->toBeFalse()
        ->and($client->can('viewAny', Customer::class))->toBeFalse()
        ->and($client->can('create', Payment::class))->toBeFalse();
});

test('admin can do everything', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    expect($admin->can('viewAny', Customer::class))->toBeTrue()
        ->and($admin->can('view', $customer))->toBeTrue()
        ->and($admin->can('update', $request))->toBeTrue()
        ->and($admin->can('delete', $request))->toBeTrue()
        ->and($admin->can('create', Payment::class))->toBeTrue();
});

test('guests cannot access admin pages', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('client cannot access admin pages', function () {
    $client = createClient();

    $response = $this->actingAs($client)->get('/dashboard');

    $response->assertStatus(403);
});

test('admin can access admin pages', function () {
    $admin = createAdmin();

    $response = $this->actingAs($admin)->get('/dashboard');

    $response->assertOk();
});
