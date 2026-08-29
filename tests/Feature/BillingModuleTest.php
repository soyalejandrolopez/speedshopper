<?php

use App\Enums\CostType;
use App\Livewire\Admin\Billing\BillingIndex;
use App\Livewire\Admin\Rates\RatesIndex;
use App\Mail\PricingRatesMail;
use App\Models\CostItem;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Services\PricingRateService;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

test('admin can view dedicated rate sheet configuration page', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->get(route('admin.rates.index'))
        ->assertOk()
        ->assertSee('Rate Sheet & Pricing PDF');
});

test('admin can view billing invoice management page', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->get(route('admin.billing.index'))
        ->assertOk()
        ->assertSee('Facturación')
        ->assertSee('Crear Nueva Factura');
});

test('admin can create a full invoice with customer, products, and initial payment', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create();

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openCreateModal')
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceForm.items', [
            [
                'product_name' => 'Nike Shoes',
                'store' => 'Nike Store',
                'quantity' => 2,
                'unit_price' => 100.0,
            ],
        ])
        ->set('invoiceForm.costs', [
            [
                'type' => 'personal_shopper',
                'description' => 'Comisión Shopper',
                'amount' => 40.0,
            ],
        ])
        ->set('invoiceForm.amount_paid', 140.0)
        ->set('invoiceForm.payment_method', 'zelle')
        ->set('invoiceForm.payment_reference', 'ZELLE-9988')
        ->call('saveInvoice')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);

    $createdRequest = PurchaseRequest::where('customer_id', $customer->id)->latest()->first();
    expect($createdRequest)->not->toBeNull()
        ->and((float) $createdRequest->total_cost)->toBe(240.0);

    $payment = Payment::where('billable_id', $createdRequest->id)->first();
    expect($payment)->not->toBeNull()
        ->and((float) $payment->amount_paid)->toBe(140.0)
        ->and((float) $payment->invoice_total)->toBe(240.0)
        ->and($payment->reference)->toBe('ZELLE-9988');
});

test('admin can record an abono payment to an invoice from billing index', function () {
    $admin = createAdmin();
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    CostItem::create([
        'costable_type' => PurchaseRequest::class,
        'costable_id' => $request->id,
        'type' => CostType::ProductCost,
        'amount' => 300.0,
    ]);

    Livewire::actingAs($admin)
        ->test(BillingIndex::class)
        ->call('openPaymentModal', $request->id)
        ->set('paymentForm.amount_paid', 150.0)
        ->set('paymentForm.payment_method', 'cash')
        ->call('savePayment')
        ->assertHasNoErrors()
        ->assertSet('showPaymentModal', false);

    $payment = Payment::where('billable_id', $request->id)->latest()->first();
    expect($payment)->not->toBeNull()
        ->and((float) $payment->amount_paid)->toBe(150.0);
});

test('admin can update rates from dedicated rates index component', function () {
    $admin = createAdmin();
    $rateService = app(PricingRateService::class);
    $current = $rateService->getRates();

    $current['extra_store_fee'] = 25.50;
    $current['box_small_heavy_duty'] = 18.00;

    Livewire::actingAs($admin)
        ->test(RatesIndex::class)
        ->set('rates', $current)
        ->call('save')
        ->assertHasNoErrors();

    $updated = $rateService->getRates();
    expect((float) $updated['extra_store_fee'])->toBe(25.50)
        ->and((float) $updated['box_small_heavy_duty'])->toBe(18.00);
});

test('admin can send pricing rates PDF via email from rates component', function () {
    Mail::fake();
    Setting::set('admin_notification_email', 'admin@speedingshopper.com');

    $admin = createAdmin();

    Livewire::actingAs($admin)
        ->test(RatesIndex::class)
        ->set('recipientEmail', 'client@example.com')
        ->set('emailLocale', 'es')
        ->set('customEmailNote', 'Aquí tienes nuestro tarifario.')
        ->call('sendRatesEmail')
        ->assertHasNoErrors()
        ->assertSet('showSendModal', false);

    Mail::assertSent(PricingRatesMail::class, function ($mail) {
        return $mail->hasTo('client@example.com') && $mail->hasBcc('admin@speedingshopper.com');
    });
});

test('client can view portal billing and pricing guide page', function () {
    $clientUser = createClient();
    $customer = $clientUser->customer;

    PurchaseRequest::factory()->create([
        'customer_id' => $customer->id,
        'product_name' => 'MacBook Pro M3',
        'unit_price' => 1299.99,
    ]);

    $this->actingAs($clientUser)
        ->get(route('portal.billing.index'))
        ->assertOk()
        ->assertSee('Guía Oficial de Precios y Facturas')
        ->assertSee('MacBook Pro M3');
});

test('unauthenticated users are redirected from billing and rates routes', function () {
    $this->get(route('admin.billing.index'))->assertRedirect(route('login'));
    $this->get(route('admin.rates.index'))->assertRedirect(route('login'));
    $this->get(route('portal.billing.index'))->assertRedirect(route('login'));
});
