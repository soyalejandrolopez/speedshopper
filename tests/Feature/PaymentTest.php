<?php

use App\Enums\PaymentMethod;
use App\Livewire\Admin\Payments\PaymentsIndex;

test('payments index renders correctly for admin', function () {
    $this->actingAs(createAdmin());

    $response = $this->get(route('admin.payments.index'));
    $response->assertOk();
});

use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
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

it('saves a payment even when amount paid is empty', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();

    Livewire::test(PaymentsIndex::class)
        ->call('openCreate')
        ->set('form.customer_id', $customer->id)
        ->set('form.invoice_total', 500)
        ->call('save')
        ->assertHasNoErrors();

    expect(Payment::count())->toBe(1)
        ->and((float) Payment::first()->amount_paid)->toBe(0.0)
        ->and((float) Payment::first()->invoice_total)->toBe(500.0);
});

it('admin can create a payment with a method and related request', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

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
        ->and($payment->billable_type)->toBe(PurchaseRequest::class)
        ->and($payment->billable_id)->toBe($request->id);
});

it('auto-populates invoice total from pending balance when selecting customer for second payment', function () {
    $this->actingAs(createAdmin());
    $customer = Customer::factory()->create();

    // First payment: invoice 1000, paid 500 => balance due 500
    Payment::factory()->create([
        'customer_id' => $customer->id,
        'invoice_total' => 1000,
        'amount_paid' => 500,
    ]);

    Livewire::test(PaymentsIndex::class)
        ->call('openCreate')
        ->call('selectCustomer', $customer->id, $customer->name)
        ->assertSet('pendingBalance', 500.0)
        ->assertSet('form.invoice_total', '500.00')
        ->assertSee(__('Balance Pendiente'))
        ->assertDontSee('Invoice Total *')
        ->set('form.amount_paid', 500)
        ->call('save')
        ->assertHasNoErrors();

    expect(Payment::count())->toBe(2);

    $secondPayment = Payment::latest('id')->first();
    expect((float) $secondPayment->invoice_total)->toBe(0.0)
        ->and((float) $secondPayment->amount_paid)->toBe(500.0);
});
