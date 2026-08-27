<?php

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use Laravel\Sanctum\Sanctum;

test('admin can create a customer via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/v1/customers', [
        'name' => 'Carlos López',
        'email' => 'carlos@example.com',
        'phone' => '+50255551234',
        'country' => 'GT',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Carlos López')
        ->assertJsonPath('data.email', 'carlos@example.com');

    $this->assertDatabaseHas('customers', ['email' => 'carlos@example.com']);
});

test('admin can update a customer via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();

    $response = $this->putJson("/api/v1/customers/{$customer->id}", [
        'name' => 'Nombre Actualizado',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Nombre Actualizado');
});

test('admin can delete a customer via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();

    $this->deleteJson("/api/v1/customers/{$customer->id}")->assertOk();

    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
});

test('admin can create a shipment with packages via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();
    $p1 = Package::factory()->create(['customer_id' => $customer->id]);
    $p2 = Package::factory()->create(['customer_id' => $customer->id]);

    $response = $this->postJson('/api/v1/shipments', [
        'customer_id' => $customer->id,
        'carrier' => 'DHL Express',
        'destination_country' => 'GT',
        'package_ids' => [$p1->id, $p2->id],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.carrier', 'DHL Express');

    $shipment = Shipment::first();
    expect($shipment->packages()->count())->toBe(2);
});

test('admin can update a shipment via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();
    $shipment = Shipment::factory()->create(['customer_id' => $customer->id]);

    $response = $this->putJson("/api/v1/shipments/{$shipment->id}", [
        'customer_id' => $customer->id,
        'carrier' => 'FedEx',
        'international_tracking' => 'TRACK123',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.carrier', 'FedEx');
});

test('admin can delete a shipment via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();
    $shipment = Shipment::factory()->create(['customer_id' => $customer->id]);

    $this->deleteJson("/api/v1/shipments/{$shipment->id}")->assertOk();

    $this->assertSoftDeleted('shipments', ['id' => $shipment->id]);
});

test('admin can create a payment via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();

    $response = $this->postJson('/api/v1/payments', [
        'customer_id' => $customer->id,
        'invoice_total' => 200.00,
        'amount_paid' => 150.00,
        'payment_method' => 'zelle',
    ]);

    $response->assertCreated();
    expect((float) $response->json('data.invoice_total'))->toBe(200.0)
        ->and((float) $response->json('data.amount_paid'))->toBe(150.0);
});

test('admin can update a payment via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();
    $payment = Payment::factory()->create([
        'customer_id' => $customer->id,
        'invoice_total' => 200,
        'amount_paid' => 50,
    ]);

    $response = $this->putJson("/api/v1/payments/{$payment->id}", [
        'customer_id' => $customer->id,
        'invoice_total' => 200.00,
        'amount_paid' => 100.00,
    ]);

    $response->assertOk();
    expect($payment->refresh()->amount_paid)->toBe('100.00');
});

test('admin can delete a payment via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();
    $payment = Payment::factory()->create(['customer_id' => $customer->id]);

    $this->deleteJson("/api/v1/payments/{$payment->id}")->assertOk();

    $this->assertSoftDeleted('payments', ['id' => $payment->id]);
});

test('admin can update a purchase request via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    $response = $this->putJson("/api/v1/requests/{$request->id}", [
        'customer_id' => $customer->id,
        'product_name' => 'Producto Actualizado',
        'quantity' => 5,
    ]);

    $response->assertOk()
        ->assertJsonPath('data.product_name', 'Producto Actualizado');
});

test('admin can delete a purchase request via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    $this->deleteJson("/api/v1/requests/{$request->id}")->assertOk();

    $this->assertSoftDeleted('purchase_requests', ['id' => $request->id]);
});

test('client can create a purchase request via API', function () {
    $client = createClient();
    Sanctum::actingAs($client);

    $response = $this->postJson('/api/v1/requests', [
        'customer_id' => $client->customer->id,
        'product_name' => 'Nike Dunk Low',
        'product_url' => 'https://nike.com/dunk',
        'quantity' => 1,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.product_name', 'Nike Dunk Low');

    $request = PurchaseRequest::first();
    expect($request->customer_id)->toBe($client->customer->id);
});

test('client cannot delete a shipment via API', function () {
    $client = createClient();
    Sanctum::actingAs($client);
    $shipment = Shipment::factory()->create(['customer_id' => $client->customer->id]);

    $this->deleteJson("/api/v1/shipments/{$shipment->id}")->assertForbidden();
});

test('user can logout via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);

    $this->postJson('/api/v1/logout')->assertOk();
});
