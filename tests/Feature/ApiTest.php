<?php

use App\Models\Customer;
use App\Models\PurchaseRequest;
use Laravel\Sanctum\Sanctum;

test('user can login via API and get token', function () {
    $admin = createAdmin();

    $response = $this->postJson('/api/v1/login', [
        'email' => $admin->email,
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);

    expect($response->json('token'))->not->toBeNull();
});

test('unauthenticated requests are rejected', function () {
    $this->getJson('/api/v1/requests')->assertUnauthorized();
});

test('admin can list all requests via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();
    PurchaseRequest::factory(3)->create(['customer_id' => $customer->id]);

    $response = $this->getJson('/api/v1/requests');

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure(['data' => [['number', 'product_name', 'status', 'customer']]]);
});

test('client only receives own requests via API', function () {
    $client = createClient();
    Sanctum::actingAs($client);
    $other = Customer::factory()->create();

    PurchaseRequest::factory(2)->create(['customer_id' => $client->customer->id]);
    PurchaseRequest::factory(5)->create(['customer_id' => $other->id]);

    $response = $this->getJson('/api/v1/requests');

    $response->assertOk()->assertJsonCount(2, 'data');
});

test('client cannot access other customers request via API', function () {
    $client = createClient();
    Sanctum::actingAs($client);
    $other = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $other->id]);

    $this->getJson("/api/v1/requests/{$request->id}")->assertForbidden();
});

test('client cannot create packages via API', function () {
    $client = createClient();
    Sanctum::actingAs($client);

    $this->postJson('/api/v1/packages', [
        'customer_id' => $client->customer->id,
        'store' => 'Amazon',
    ])->assertForbidden();
});

test('admin can create package via API', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);
    $customer = Customer::factory()->create();

    $response = $this->postJson('/api/v1/packages', [
        'customer_id' => $customer->id,
        'store' => 'Amazon',
        'original_tracking' => '1Z99999999',
        'weight_lb' => 3.5,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.number', fn ($n) => str_starts_with($n, 'PKG-'));
});

test('clients cannot see admin-only fields like notes in API', function () {
    $client = createClient();
    Sanctum::actingAs($client);
    $request = PurchaseRequest::factory()->create([
        'customer_id' => $client->customer->id,
        'notes' => 'Nota privada de la dueña',
    ]);

    $response = $this->getJson("/api/v1/requests/{$request->id}");

    $response->assertOk()->assertJsonMissing(['notes' => 'Nota privada de la dueña']);
});

test('me endpoint returns authenticated user', function () {
    $admin = createAdmin();
    Sanctum::actingAs($admin);

    $this->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('email', $admin->email);
});
