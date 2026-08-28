<?php

use App\Enums\PaymentMethod;
use App\Livewire\Admin\Payments\PaymentsIndex;
use App\Models\Customer;
use App\Models\Payment;
use Livewire\Livewire;

it('selects a customer through the search suggestions', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();

    Livewire::test(PaymentsIndex::class)
        ->call('openCreate')
        ->call('selectCustomer', $customer->id, $customer->name)
        ->assertSet('form.customer_id', $customer->id)
        ->assertSet('form.customer_search', $customer->name);
});

it('admin can create a payment without selecting a method', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();

    Livewire::test(PaymentsIndex::class)
        ->call('openCreate')
        ->set('form.customer_id', $customer->id)
        ->set('form.invoice_total', 100)
        ->set('form.amount_paid', 80)
        ->call('save')
        ->assertHasNoErrors();

    expect(Payment::count())->toBe(1);

    $payment = Payment::first();

    expect($payment->customer_id)->toBe($customer->id)
        ->and($payment->payment_method)->toBeNull()
        ->and((float) $payment->invoice_total)->toBe(100.0)
        ->and((float) $payment->amount_paid)->toBe(80.0);
});

it('admin can create a payment with a method and related request', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $request = \App\Models\PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    Livewire::test(PaymentsIndex::class)
        ->call('openCreate')
        ->set('form.customer_id', $customer->id)
        ->set('form.invoice_total', 50)
        ->set('form.amount_paid', 50)
        ->set('form.payment_method', PaymentMethod::Zelle->value)
        ->set('form.billable_type', 'purchase_request')
        ->set('form.billable_id', $request->id)
        ->call('save')
        ->assertHasNoErrors();

    $payment = Payment::first();

    expect($payment->payment_method)->toBe(PaymentMethod::Zelle)
        ->and($payment->billable_type)->toBe(\App\Models\PurchaseRequest::class)
        ->and($payment->billable_id)->toBe($request->id);
});
