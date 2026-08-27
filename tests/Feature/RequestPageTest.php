<?php

use App\Livewire\ChatRequestForm;
use App\Livewire\PublicRequestForm;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use Livewire\Livewire;

it('renders the request page with header, footer and the chatbot', function () {
    seedRoles();

    $this->get(route('request'))
        ->assertOk()
        ->assertSee(__('Send us your purchase request'))
        ->assertSee(__('Chat with the assistant'))
        ->assertSeeVolt('chat-request-form');
});

it('guides the visitor step by step and creates the customer and request at the end', function () {
    $component = Livewire::test(ChatRequestForm::class)
        ->assertSet('step', 1)
        ->assertSet('started', false)
        ->call('start')
        ->assertSet('started', true)

        ->set('currentAnswer', 'María González')
        ->call('next')
        ->assertSet('step', 2)
        ->assertSet('name', 'María González')

        ->set('currentAnswer', 'maria@example.com')
        ->call('next')
        ->assertSet('step', 3)
        ->assertSet('email', 'maria@example.com')

        ->set('currentAnswer', '+50255550123')
        ->call('next')
        ->assertSet('step', 4)
        ->assertSet('whatsapp', '+50255550123')

        ->set('currentAnswer', 'Nike Air Max 270 — Talla 7.5')
        ->call('next')
        ->assertSet('step', 5)
        ->assertSet('product_name', 'Nike Air Max 270 — Talla 7.5')

        ->set('currentAnswer', 'https://nike.com/airmax')
        ->call('next')
        ->assertSet('step', 6)
        ->assertSet('product_url', 'https://nike.com/airmax')

        ->set('currentAnswer', 'Color negro')
        ->call('next')
        ->assertSet('finished', true);

    expect($component->instance()->progressPercent())->toBe(100);

    $customer = Customer::where('email', 'maria@example.com')->first();

    expect($customer)->not->toBeNull()
        ->and($customer->name)->toBe('María González')
        ->and($customer->whatsapp)->toBe('+50255550123');

    $request = PurchaseRequest::where('customer_id', $customer->id)->first();

    expect($request)->not->toBeNull()
        ->and($request->product_name)->toBe('Nike Air Max 270 — Talla 7.5')
        ->and($request->product_url)->toBe('https://nike.com/airmax')
        ->and($request->description)->toBe('Color negro')
        ->and($request->status->value)->toBe('new');
});

it('allows going back to the previous question', function () {
    Livewire::test(ChatRequestForm::class)
        ->call('start')
        ->set('currentAnswer', 'María')
        ->call('next')
        ->set('currentAnswer', 'maria@example.com')
        ->call('next')
        ->assertSet('step', 3)
        ->assertSet('email', 'maria@example.com')
        ->call('back')
        ->assertSet('step', 2)
        ->assertSet('currentAnswer', 'maria@example.com');
});

it('validates required answers in the chatbot', function () {
    Livewire::test(ChatRequestForm::class)
        ->call('next')
        ->assertHasErrors('currentAnswer');

    Livewire::test(ChatRequestForm::class)
        ->set('currentAnswer', 'María')
        ->call('next')
        ->set('currentAnswer', 'not-an-email')
        ->call('next')
        ->assertHasErrors('currentAnswer');
});

it('creates a customer and a request from the page form', function () {
    Livewire::test(PublicRequestForm::class)
        ->set('form.name', 'María González')
        ->set('form.email', 'maria@example.com')
        ->set('form.whatsapp', '+50255550123')
        ->set('form.product_name', 'Nike Air Max 270 — Talla 7.5')
        ->set('form.product_url', 'https://nike.com/airmax')
        ->set('form.description', 'Color negro')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    $customer = Customer::where('email', 'maria@example.com')->first();

    expect($customer)->not->toBeNull()
        ->and($customer->name)->toBe('María González')
        ->and($customer->whatsapp)->toBe('+50255550123');

    $request = PurchaseRequest::where('customer_id', $customer->id)->first();

    expect($request)->not->toBeNull()
        ->and($request->product_name)->toBe('Nike Air Max 270 — Talla 7.5')
        ->and($request->product_url)->toBe('https://nike.com/airmax')
        ->and($request->status->value)->toBe('new');
});

it('requires a name, email and product on the request form', function () {
    Livewire::test(PublicRequestForm::class)
        ->call('submit')
        ->assertHasErrors(['form.name', 'form.email', 'form.product_name']);
});
