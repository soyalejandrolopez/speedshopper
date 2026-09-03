<?php

use App\Enums\CostType;
use App\Livewire\ClientRegistrationForm;
use App\Models\Customer;
use App\Models\PurchaseRequest;
use Livewire\Livewire;

it('calculates personal shopper costs accurately without online shopping overlap', function () {
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
        ->and($request->total_cost)->toBe(1380.0) // 1200 product + 180 shopper fee
        ->and($request->costItems->where('type', CostType::ReceivingFee)->count())->toBe(0)
        ->and($request->costItems->where('description', 'like', '%Valor Pagado en Internet%')->count())->toBe(0);
});

it('does not charge product price in total invoice when service is online shopping', function () {
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
    $internetCost = $request->costItems->firstWhere('type', CostType::ProductCost);
    expect($internetCost)->not->toBeNull()
        ->and((float) $internetCost->amount)->toBe(0.0);

    // Total cost on invoice should only be 15% ($180) + delivery ($20) = $200.00
    expect((float) $request->total_cost)->toBe(200.0);
});

it('switches between personal_shopper and online_shopping without superposition', function () {
    Livewire::test(ClientRegistrationForm::class)
        ->call('selectService', 'personal_shopper')
        ->assertSet('form.services', ['personal_shopper'])
        ->call('selectService', 'online_shopping')
        ->assertSet('form.services', ['online_shopping'])
        ->call('selectService', 'repack')
        ->assertSet('form.services', ['online_shopping', 'repack'])
        ->call('selectService', 'personal_shopper')
        ->assertSet('form.services', ['repack', 'personal_shopper']);
});
