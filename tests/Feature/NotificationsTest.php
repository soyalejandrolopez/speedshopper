<?php

use App\Livewire\ClientRegistrationForm;
use App\Livewire\ContactForm;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewContactInquiryNotification;
use App\Notifications\NewPurchaseRequestNotification;
use App\Notifications\StatusChangedNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

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
    Setting::set('notify_whatsapp', '1');
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

    Setting::set('notify_whatsapp', '1');
    Setting::set('whatsapp_api_url', 'https://wa-gateway.test/send');

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

    Setting::set('notify_whatsapp', '1');
    Setting::set('whatsapp_api_url', 'https://wa-gateway.test/send');

    $request = PurchaseRequest::factory()->create([
        'customer_id' => Customer::factory()->create(['email' => null, 'user_id' => null])->id,
    ]);

    $request->transitionTo('quoted');

    Http::assertNothingSent();
});

it('sends an email notification to the administrator when a purchase request is submitted', function () {
    seedRoles();
    Notification::fake();
    Setting::set('admin_notification_email', 'admin@speedshopper.com');

    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Laura Castillo')
        ->set('form.email', 'laura@example.com')
        ->set('form.whatsapp', '+584120001122')
        ->set('form.country', 'VE')
        ->set('form.services', ['online_shopping'])
        ->call('next')
        ->set('form.products', 'Cartera Michael Kors')
        ->set('form.budget', '250')
        ->call('next')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors();

    Notification::assertSentOnDemand(NewPurchaseRequestNotification::class);
});

it('sends an email notification to the administrator when a contact inquiry is submitted', function () {
    seedRoles();
    Notification::fake();
    Setting::set('admin_notification_email', 'admin@speedshopper.com');

    Livewire::test(ContactForm::class)
        ->set('form.name', 'Pedro Páramo')
        ->set('form.email', 'pedro@example.com')
        ->set('form.message', 'Quiero saber cuánto cuesta el envío a México.')
        ->call('submit')
        ->assertHasNoErrors();

    Notification::assertSentOnDemand(NewContactInquiryNotification::class);
});

it('sends email notifications with pdf attached to both customer and admin when a request is submitted', function () {
    seedRoles();
    Notification::fake();
    Setting::set('admin_notification_email', 'admin@speedshopper.com');

    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Carlos Mendoza')
        ->set('form.email', 'carlos@example.com')
        ->set('form.whatsapp', '+584125556677')
        ->set('form.country', 'VE')
        ->set('form.services', ['personal_shopper'])
        ->call('next')
        ->set('form.products', 'PlayStation 5')
        ->set('form.budget', '499.99')
        ->call('next')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors();

    Notification::assertSentOnDemand(NewPurchaseRequestNotification::class);
    Notification::assertSentOnDemand(\App\Notifications\ClientPurchaseRequestConfirmationNotification::class);
});
