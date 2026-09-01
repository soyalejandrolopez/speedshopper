<?php

use App\Livewire\ChatRequestForm;
use App\Livewire\ClientRegistrationForm;
use App\Livewire\PublicRequestForm;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use App\Models\User;
use Livewire\Livewire;

it('renders the request page with header, footer and the registration form', function () {
    seedRoles();

    $this->get(route('request'))
        ->assertOk()
        ->assertSee(__('Send us your purchase request'))
        ->assertSee(__('Client Registration'))
        ->assertSeeLivewire(ClientRegistrationForm::class);
});

it('registers a client and creates a request through the 3-step form', function () {
    app()->setLocale('es');

    $component = Livewire::test(ClientRegistrationForm::class)
        ->assertSet('step', 1)
        ->set('form.name', 'María González')
        ->set('form.whatsapp', '+50255550123')
        ->set('form.email', 'maria@example.com')
        ->set('form.country', 'GT')
        ->set('form.city', 'Guatemala')
        ->set('form.address', 'Zona 10')
        ->set('form.services', ['online_shopping', 'consolidation'])
        ->call('next')
        ->assertSet('step', 2)

        ->set('form.products', 'Nike Air Max 270, Zapatos Zara')
        ->set('form.preferred_stores', 'Nike, Zara')
        ->set('form.budget', '200')
        ->set('form.find_deals', 'yes')
        ->call('next')
        ->assertSet('step', 3)

        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect($component->instance()->progressPercent())->toBe(100);

    $customer = Customer::where('email', 'maria@example.com')->first();

    expect($customer)->not->toBeNull()
        ->and($customer->name)->toBe('María González')
        ->and($customer->whatsapp)->toBe('+50255550123')
        ->and($customer->country)->toBe('GT');

    $request = PurchaseRequest::where('customer_id', $customer->id)->first();

    expect($request)->not->toBeNull()
        ->and($request->status->value)->toBe('new')
        ->and($request->services)->toContain('online_shopping')
        ->and($request->services)->toContain('consolidation')
        ->and($request->description)->toContain('Comprar Online')
        ->and($request->description)->toContain('Nike Air Max 270, Zapatos Zara')
        ->and((float) $request->unit_price)->toBe(200.0);
});

it('creates a package in Operations when the client already purchased', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Carlos López')
        ->set('form.whatsapp', '+50255550124')
        ->set('form.email', 'carlos@example.com')
        ->set('form.country', 'GT')
        ->set('form.services', ['package_reception'])
        ->call('next')
        ->set('form.products', 'Zapatos')
        ->set('form.already_purchased', 'yes')
        ->set('form.store_name', 'Nike')
        ->set('form.tracking_number', 'TRACK123')
        ->call('next')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors();

    $customer = Customer::where('email', 'carlos@example.com')->first();
    $request = PurchaseRequest::where('customer_id', $customer->id)->first();

    expect($customer->packages()->count())->toBe(1);

    $package = $customer->packages()->first();

    expect($package->store)->toBe('Nike')
        ->and($package->original_tracking)->toBe('TRACK123')
        ->and($package->purchase_request_id)->toBe($request->id);
});

it('creates a draft shipment with shipping preferences and links the package', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Sofía Ramírez')
        ->set('form.whatsapp', '+50255550125')
        ->set('form.email', 'sofia@example.com')
        ->set('form.country', 'SV')
        ->set('form.services', ['package_reception', 'delivery_to_courier'])
        ->call('next')
        ->set('form.products', 'Ropa')
        ->set('form.already_purchased', 'yes')
        ->set('form.store_name', 'Zara')
        ->set('form.tracking_number', 'TRACK999')
        ->call('next')
        ->set('form.courier', 'yes')
        ->set('form.courier_name', 'Tracargo')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors();

    $customer = Customer::where('email', 'sofia@example.com')->first();
    $package = $customer->packages()->first();
    $shipment = $customer->shipments()->first();

    expect($shipment)->not->toBeNull()
        ->and($shipment->status->value)->toBe('draft')
        ->and($shipment->carrier)->toBe('Tracargo')
        ->and($shipment->destination_country)->toBe('SV')
        ->and($shipment->packages()->count())->toBe(1)
        ->and($shipment->packages()->first()->id)->toBe($package->id);
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
        ->set('items.0.product_name', 'Nike Air Max 270 — Talla 7.5')
        ->set('items.0.product_url', 'https://nike.com/airmax')
        ->set('items.0.description', 'Color negro')
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

it('creates multiple purchase requests at once when multiple products are added', function () {
    Livewire::test(PublicRequestForm::class)
        ->set('form.name', 'Carlos Gómez')
        ->set('form.email', 'carlos@example.com')
        ->set('form.whatsapp', '+50255559999')
        ->set('items.0.product_name', 'Zapatos Nike')
        ->set('items.0.product_url', 'https://nike.com')
        ->call('addItem')
        ->set('items.1.product_name', 'Camisa Adidas')
        ->set('items.1.product_url', 'https://adidas.com')
        ->call('addItem')
        ->set('items.2.product_name', 'Gorra Puma')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true)
        ->assertSet('createdCount', 3);

    $customer = Customer::where('email', 'carlos@example.com')->first();
    expect($customer)->not->toBeNull();

    $requests = PurchaseRequest::where('customer_id', $customer->id)->get();
    expect($requests)->toHaveCount(3)
        ->and($requests->pluck('product_name')->toArray())->toBe(['Zapatos Nike', 'Camisa Adidas', 'Gorra Puma']);
});

it('can remove a product from the list', function () {
    Livewire::test(PublicRequestForm::class)
        ->set('items.0.product_name', 'Producto 1')
        ->call('addItem')
        ->set('items.1.product_name', 'Producto 2')
        ->assertCount('items', 2)
        ->call('removeItem', 0)
        ->assertCount('items', 1)
        ->assertSet('items.0.product_name', 'Producto 2');
});

it('requires a name, email and product on the request form', function () {
    Livewire::test(PublicRequestForm::class)
        ->set('items.0.product_name', '')
        ->call('submit')
        ->assertHasErrors(['form.name', 'form.email', 'items.0.product_name']);
});

it('calculates packaging sum and creates cost items in ClientRegistrationForm', function () {
    $component = Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Laura Martínez')
        ->set('form.whatsapp', '+50255551111')
        ->set('form.email', 'laura@example.com')
        ->set('form.country', 'GT')
        ->set('form.services', ['online_shopping'])
        ->call('next')
        ->set('form.products', 'Shein Clothes Pack')
        ->call('incrementBox', 'small') // 1 small ($15)
        ->call('incrementBox', 'medium') // 1 medium ($20)
        ->call('incrementBox', 'medium') // 2 medium ($40)
        ->call('incrementBox', 'large'); // 1 large ($25)

    // Total: 15 + 40 + 25 = $80
    expect($component->instance()->packagingTotal)->toBe(80.0);

    $component->call('next')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors();

    $customer = Customer::where('email', 'laura@example.com')->first();
    $request = PurchaseRequest::where('customer_id', $customer->id)->first();

    expect($request)->not->toBeNull()
        ->and($request->services)->toContain('repack')
        ->and((float) $request->total_cost)->toBe(100.0)
        ->and($request->costItems)->toHaveCount(4);
});

it('calculates packaging sum and creates cost items in PublicRequestForm', function () {
    $component = Livewire::test(PublicRequestForm::class)
        ->set('form.name', 'Pedro Morales')
        ->set('form.email', 'pedro@example.com')
        ->set('form.whatsapp', '+50255552222')
        ->set('items.0.product_name', 'Amazon Echo Dot')
        ->call('incrementBox', 'small') // 1 small ($15)
        ->call('incrementBox', 'large'); // 1 large ($25)

    // Total: 15 + 25 = $40
    expect($component->instance()->packagingTotal)->toBe(40.0);

    $component->call('submit')
        ->assertHasNoErrors();

    $customer = Customer::where('email', 'pedro@example.com')->first();
    $request = PurchaseRequest::where('customer_id', $customer->id)->first();

    expect($request)->not->toBeNull()
        ->and($request->services)->toContain('packing')
        ->and((float) $request->total_cost)->toBe(40.0)
        ->and($request->costItems)->toHaveCount(2);
});

it('allows customer to create a user account during ClientRegistrationForm submission', function () {
    seedRoles();

    expect(auth()->check())->toBeFalse();

    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Carlos Mendoza')
        ->set('form.whatsapp', '+50255553333')
        ->set('form.email', 'carlos.mendoza@example.com')
        ->set('form.country', 'GT')
        ->set('form.services', ['personal_shopper'])
        ->set('form.create_account', true)
        ->set('form.password', 'secret1234')
        ->set('form.password_confirmation', 'secret1234')
        ->call('next')
        ->set('form.products', 'MacBook Air M2')
        ->call('next')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(auth()->check())->toBeTrue();

    $user = User::where('email', 'carlos.mendoza@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasRole('client'))->toBeTrue()
        ->and(auth()->id())->toBe($user->id);

    $customer = Customer::where('email', 'carlos.mendoza@example.com')->first();
    expect($customer)->not->toBeNull()
        ->and($customer->user_id)->toBe($user->id);
});

it('allows customer to create a user account during PublicRequestForm submission', function () {
    seedRoles();

    expect(auth()->check())->toBeFalse();

    Livewire::test(PublicRequestForm::class)
        ->set('form.name', 'Elena Rojas')
        ->set('form.email', 'elena.rojas@example.com')
        ->set('form.whatsapp', '+50255554444')
        ->set('form.create_account', true)
        ->set('form.password', 'secret1234')
        ->set('form.password_confirmation', 'secret1234')
        ->set('items.0.product_name', 'Kindle Paperwhite')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(auth()->check())->toBeTrue();

    $user = User::where('email', 'elena.rojas@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasRole('client'))->toBeTrue()
        ->and(auth()->id())->toBe($user->id);

    $customer = Customer::where('email', 'elena.rojas@example.com')->first();
    expect($customer)->not->toBeNull()
        ->and($customer->user_id)->toBe($user->id);
});

it('accepts 4 character minimum password in registration form and rejects less than 4', function () {
    seedRoles();

    // Less than 4 characters should fail validation
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Mario Bros')
        ->set('form.whatsapp', '+50255551111')
        ->set('form.email', 'mario@example.com')
        ->set('form.country', 'GT')
        ->set('form.create_account', true)
        ->set('form.password', '123')
        ->set('form.password_confirmation', '123')
        ->call('next')
        ->assertHasErrors(['form.password' => 'min']);

    // 4 characters should pass validation
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Mario Bros')
        ->set('form.whatsapp', '+50255551111')
        ->set('form.email', 'mario@example.com')
        ->set('form.country', 'GT')
        ->set('form.create_account', true)
        ->set('form.password', '1234')
        ->set('form.password_confirmation', '1234')
        ->call('next')
        ->assertHasNoErrors(['form.password', 'form.password_confirmation']);
});
