<?php

use App\Enums\CostType;
use App\Livewire\ClientRegistrationForm;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use Livewire\Livewire;

it('calculates personal shopper costs accurately when requested alone', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Carlos Shopper')
        ->set('form.email', 'carlos.shopper@example.com')
        ->set('form.whatsapp', '+50255551122')
        ->set('form.services', ['personal_shopper'])
        ->set('form.products', 'Laptop Dell XPS 15')
        ->set('form.preferred_stores', 'Best Buy')
        ->set('form.budget', '1200.00')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    $customer = Customer::where('email', 'carlos.shopper@example.com')->first();
    expect($customer)->not->toBeNull();

    $request = PurchaseRequest::where('customer_id', $customer->id)->first();
    expect($request)->not->toBeNull()
        ->and((float) $request->total_cost)->toBe(1380.0) // 1200 product + 180 shopper fee
        ->and($request->costItems->where('type', CostType::ReceivingFee)->count())->toBe(0)
        ->and($request->costItems->where('description', 'like', '%Valor Pagado en Internet%')->count())->toBe(0);
});

it('does not charge product price in total invoice when service is online shopping alone', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Laura Online')
        ->set('form.email', 'laura.online@example.com')
        ->set('form.whatsapp', '+50255553344')
        ->set('form.services', ['online_shopping'])
        ->set('form.products', 'Zapatos Nike')
        ->set('form.budget', '1200.00')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    $customer = Customer::where('email', 'laura.online@example.com')->first();
    $request = PurchaseRequest::where('customer_id', $customer->id)->first();

    expect($request)->not->toBeNull();

    // The product cost item should have amount = 0.00 (not charged on invoice)
    $internetCost = $request->costItems->first(fn ($i) => str_contains($i->description, 'Valor Pagado en Internet'));
    expect($internetCost)->not->toBeNull()
        ->and((float) $internetCost->amount)->toBe(0.0);

    // Total cost on invoice should only be 15% ($180) + delivery ($20) = $200.00
    expect((float) $request->total_cost)->toBe(200.0);
});

it('preserves warehouse commission and delivery fee without product cost duplication when both services are selected', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->set('form.name', 'Andrea Both')
        ->set('form.email', 'andrea.both@example.com')
        ->set('form.whatsapp', '+50255556677')
        ->set('form.services', ['personal_shopper', 'online_shopping'])
        ->set('form.products', 'Computadora Apple')
        ->set('form.budget', '1200.00')
        ->set('form.confirm_correct', true)
        ->set('form.accept_costs', true)
        ->set('form.accept_contact', true)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    $customer = Customer::where('email', 'andrea.both@example.com')->first();
    $request = PurchaseRequest::where('customer_id', $customer->id)->first();

    expect($request)->not->toBeNull()
        ->and($request->services)->toContain('personal_shopper', 'online_shopping');

    // Product Cost is charged only once ($1200.00)
    $productCost = $request->costItems->first(fn ($i) => str_contains($i->description, 'Valor de Productos'));
    expect($productCost)->not->toBeNull()
        ->and((float) $productCost->amount)->toBe(1200.0);

    // Personal Shopper Commission is charged ($180.00)
    $shopperFee = $request->costItems->firstWhere('type', CostType::ShopperFee);
    expect($shopperFee)->not->toBeNull()
        ->and((float) $shopperFee->amount)->toBe(180.0);

    // Online Shopping product cost is $0.00 (not duplicated!)
    $internetCost = $request->costItems->first(fn ($i) => str_contains($i->description, 'Valor Pagado en Internet'));
    expect($internetCost)->not->toBeNull()
        ->and((float) $internetCost->amount)->toBe(0.0);

    // Warehouse commission is NOT eliminated ($180.00)
    $warehouseComm = $request->costItems->first(fn ($i) => str_contains($i->description, 'Comisión Almacén'));
    expect($warehouseComm)->not->toBeNull()
        ->and((float) $warehouseComm->amount)->toBe(180.0);

    // Warehouse delivery is NOT eliminated ($20.00)
    $deliveryFee = $request->costItems->first(fn ($i) => str_contains($i->description, 'Traslado'));
    expect($deliveryFee)->not->toBeNull()
        ->and((float) $deliveryFee->amount)->toBe(20.0);

    // Total: 1200 + 180 + 0 + 180 + 20 = 1580.00
    expect((float) $request->total_cost)->toBe(1580.0);
});

it('allows multi-selection toggle in selectService', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->call('selectService', 'personal_shopper')
        ->assertSet('form.services', ['personal_shopper'])
        ->call('selectService', 'online_shopping')
        ->assertSet('form.services', ['personal_shopper', 'online_shopping'])
        ->call('selectService', 'repack')
        ->assertSet('form.services', ['personal_shopper', 'online_shopping', 'repack'])
        ->call('selectService', 'personal_shopper')
        ->assertSet('form.services', ['online_shopping', 'repack']);
});
