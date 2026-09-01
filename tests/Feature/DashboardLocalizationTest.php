<?php

beforeEach(function () {
    seedRoles();
});

test('locale switcher updates session and redirects back', function () {
    $response = $this->get(route('locale.switch', 'en'));
    $response->assertSessionHas('locale', 'en');

    $response = $this->get(route('locale.switch', 'es'));
    $response->assertSessionHas('locale', 'es');
});

test('admin dashboard renders in english when locale is en', function () {
    app()->setLocale('en');
    $admin = createAdmin();

    $this->actingAs($admin)
        ->withSession(['locale' => 'en'])
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Total Customers')
        ->assertSee('Total Requests')
        ->assertSee('Total Packages')
        ->assertSee('Total Shipments')
        ->assertSee('Payments')
        ->assertSee('Invoicing')
        ->assertSee('Pending Balance')
        ->assertSee('Contact Messages');
});

test('client portal dashboard renders in english when locale is en', function () {
    app()->setLocale('en');
    $client = createClient();

    $this->actingAs($client)
        ->withSession(['locale' => 'en'])
        ->get(route('portal.dashboard'))
        ->assertOk()
        ->assertSee('My Account')
        ->assertSee('Client Portal')
        ->assertSee('Pending Balance')
        ->assertSee('Pay Balance')
        ->assertSee('Your orders')
        ->assertSee('Your boxes');
});

test('admin customer and package index views render in english when locale is en', function () {
    app()->setLocale('en');
    $admin = createAdmin();

    $this->actingAs($admin)
        ->withSession(['locale' => 'en'])
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertSee('Total Customers')
        ->assertSee('With Requests')
        ->assertSee('With Packages')
        ->assertSee('With Shipments');

    $this->actingAs($admin)
        ->withSession(['locale' => 'en'])
        ->get(route('admin.packages.index'))
        ->assertOk()
        ->assertSee('Total Packages')
        ->assertSee('Received today')
        ->assertSee('Stored Packages')
        ->assertSee('Ready to ship');
});
