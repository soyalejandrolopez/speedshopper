<?php

use App\Models\Customer;
use App\Models\Package;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\StatusChangedNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

it('notifies the customer by email when a request changes status', function () {
    seedRoles();
    Notification::fake();

    $user = User::factory()->create(['email' => 'maria@example.com']);
    $user->assignRole('client');
    $customer = Customer::factory()->create(['user_id' => $user->id, 'email' => $user->email]);
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    $request->transitionTo('quoted');

    Notification::assertSentOnDemand(StatusChangedNotification::class);
});

it('does not notify when there is no customer contact', function () {
    seedRoles();
    Notification::fake();

    $request = PurchaseRequest::factory()->create([
        'customer_id' => Customer::factory()->create(['email' => null, 'user_id' => null])->id,
    ]);

    $request->transitionTo('quoted');

    Notification::assertNothingSent();
});

it('sends a whatsapp notification when enabled and customer has whatsapp', function () {
    seedRoles();
    \App\Models\Setting::set('notify_whatsapp', '1');
    Notification::fake();

    $user = User::factory()->create(['email' => 'maria@example.com']);
    $user->assignRole('client');
    $customer = Customer::factory()->create(['user_id' => $user->id, 'email' => $user->email, 'whatsapp' => '+50255550123']);
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    $request->transitionTo('quoted');

    Notification::assertSentOnDemand(StatusChangedNotification::class, 2);
});

it('posts the whatsapp message to the configured api', function () {
    seedRoles();
    Http::fake();

    \App\Models\Setting::set('notify_whatsapp', '1');
    \App\Models\Setting::set('whatsapp_api_url', 'https://wa-gateway.test/send');

    $user = User::factory()->create(['email' => 'maria@example.com']);
    $user->assignRole('client');
    $customer = Customer::factory()->create(['user_id' => $user->id, 'email' => $user->email, 'whatsapp' => '+50255550123']);
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    $request->transitionTo('quoted');

    Http::assertSent(fn ($req) => $req->url() === 'https://wa-gateway.test/send'
        && $req['phone'] === '+50255550123'
        && str_contains($req['message'], $request->number));
});

it('does not call the whatsapp api when no phone is available', function () {
    seedRoles();
    Http::fake();
    Notification::fake();

    \App\Models\Setting::set('notify_whatsapp', '1');
    \App\Models\Setting::set('whatsapp_api_url', 'https://wa-gateway.test/send');

    $request = PurchaseRequest::factory()->create([
        'customer_id' => Customer::factory()->create(['email' => null, 'user_id' => null])->id,
    ]);

    $request->transitionTo('quoted');

    Http::assertNothingSent();
});
