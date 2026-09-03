<?php

use App\Enums\CostType;
use App\Enums\PaymentMethod;
use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;
use App\Livewire\Admin\Billing\BillingIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Payments\PaymentsIndex;
use App\Livewire\Admin\Reports\ReportsIndex;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use App\Services\FinanceService;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = createAdmin();
});

test('finance service calculates exact unified totals for requests, shipments, and payments', function () {
    $customer1 = Customer::factory()->create(['name' => 'Cliente A']);
    $customer2 = Customer::factory()->create(['name' => 'Cliente B']);

    // 1. Purchase Request with cost items ($150 total)
    $req = PurchaseRequest::create([
        'customer_id' => $customer1->id,
        'product_name' => 'Producto 1',
        'status' => RequestStatus::Quoted,
    ]);
    $req->costItems()->createMany([
        ['type' => CostType::ProductCost, 'description' => 'Item', 'amount' => 100.00],
        ['type' => CostType::ShopperFee, 'description' => 'Fee', 'amount' => 50.00],
    ]);

    // 2. Shipment with shipping cost ($80 total)
    $shipment = Shipment::create([
        'customer_id' => $customer1->id,
        'carrier' => 'DHL',
        'destination_country' => 'GT',
        'shipping_cost' => 80.00,
        'status' => ShipmentStatus::InTransit,
    ]);

    // 3. Payment for shipment ($80 invoice, $40 paid)
    Payment::create([
        'customer_id' => $customer1->id,
        'billable_type' => Shipment::class,
        'billable_id' => $shipment->id,
        'invoice_total' => 80.00,
        'amount_paid' => 40.00,
        'payment_method' => PaymentMethod::Zelle,
    ]);

    // 4. Standalone direct payment for customer 2 ($200 invoice, $150 paid)
    Payment::create([
        'customer_id' => $customer2->id,
        'invoice_total' => 200.00,
        'amount_paid' => 150.00,
        'payment_method' => PaymentMethod::Card,
    ]);

    $finance = app(FinanceService::class);
    $metrics = $finance->getMetrics();

    // Expected:
    // Total Invoiced = $150 (req) + $80 (shipment) + $200 (standalone payment) = $430.00
    // Total Collected = $40 (shipment payment) + $150 (standalone payment) = $190.00
    // Total Balance Due = $430.00 - $190.00 = $240.00
    // Customer 1 balance: ($150 + $80) - $40 = $190.00
    // Customer 2 balance: $200 - $150 = $50.00
    // Total customer balance = $190 + $50 = $240.00
    expect($metrics['total_invoiced'])->toBe(430.0)
        ->and($metrics['total_collected'])->toBe(190.0)
        ->and($metrics['total_balance_due'])->toBe(240.0)
        ->and($customer1->balance_due)->toBe(190.0)
        ->and($customer2->balance_due)->toBe(50.0);
});

test('dashboard, billing, and payments sections render synchronized kpi totals', function () {
    $this->actingAs($this->admin);

    $customer = Customer::factory()->create(['name' => 'María López']);

    $req = PurchaseRequest::create([
        'customer_id' => $customer->id,
        'product_name' => 'Zapatos',
        'status' => RequestStatus::Purchased,
    ]);
    $req->costItems()->createMany([
        ['type' => CostType::ProductCost, 'description' => 'Item', 'amount' => 300.00],
        ['type' => CostType::ShopperFee, 'description' => 'Fee', 'amount' => 60.00],
    ]);

    Payment::create([
        'customer_id' => $customer->id,
        'billable_type' => PurchaseRequest::class,
        'billable_id' => $req->id,
        'invoice_total' => 360.00,
        'amount_paid' => 200.00,
        'payment_method' => PaymentMethod::Zelle,
    ]);

    $finance = app(FinanceService::class)->getMetrics();

    // Verify Dashboard view
    Livewire::test(Dashboard::class)
        ->assertViewHas('totalInvoiced', $finance['total_invoiced'])
        ->assertViewHas('totalPaid', $finance['total_collected'])
        ->assertViewHas('totalBalanceDue', $finance['total_balance_due']);

    // Verify Billing section view
    Livewire::test(BillingIndex::class)
        ->assertViewHas('totalInvoiced', $finance['total_invoiced'])
        ->assertViewHas('totalCollected', $finance['total_collected'])
        ->assertViewHas('totalPending', $finance['total_balance_due']);

    // Verify Payments section view
    Livewire::test(PaymentsIndex::class)
        ->assertViewHas('totalInvoiced', $finance['total_invoiced'])
        ->assertViewHas('totalCollected', $finance['total_collected'])
        ->assertViewHas('totalBalanceDue', $finance['total_balance_due']);
});

test('creating invoice with zero paid creates payment ledger record and balances match exactly', function () {
    $this->actingAs($this->admin);

    $customer = Customer::factory()->create(['name' => 'Franchesca Liccien']);

    Livewire::test(BillingIndex::class)
        ->set('invoiceForm.customer_id', $customer->id)
        ->set('invoiceType', 'pendiente')
        ->set('invoiceForm.items', [
            ['product_name' => 'Vestido', 'store' => 'Zara', 'quantity' => 1, 'unit_price' => 400.00],
        ])
        ->set('invoiceForm.costs', [
            ['type' => CostType::ShopperFee->value, 'description' => 'Servicio', 'amount' => 61.87],
        ])
        ->set('invoiceForm.amount_paid', 0.00)
        ->set('invoiceForm.payment_method', PaymentMethod::Zelle->value)
        ->call('saveInvoice')
        ->assertHasNoErrors();

    $payment = Payment::where('customer_id', $customer->id)->first();
    expect($payment)->not->toBeNull()
        ->and((float) $payment->invoice_total)->toBeGreaterThan(0.0)
        ->and((float) $payment->amount_paid)->toBe(0.0)
        ->and((float) $payment->balance_due)->toBe((float) $payment->invoice_total);

    // Payments index renders the record and balance matches
    Livewire::test(PaymentsIndex::class)
        ->assertSee('Franchesca Liccien')
        ->assertSee(money($payment->balance_due));
});

test('billing index renders standalone direct invoices alongside request invoices', function () {
    $this->actingAs($this->admin);

    $customer = Customer::factory()->create(['name' => 'Rosa Cordova']);

    $directInvoice = Payment::create([
        'customer_id' => $customer->id,
        'invoice_total' => 1941.60,
        'amount_paid' => 1668.35,
        'payment_method' => PaymentMethod::Zelle,
        'notes' => 'Factura Directa Rosa',
    ]);

    Livewire::test(BillingIndex::class)
        ->assertSee($directInvoice->number)
        ->assertSee('Rosa Cordova')
        ->assertSee('Directa')
        ->assertSee('$1,941.60')
        ->assertSee('$1,668.35')
        ->assertSee('$273.25');
});

test('reports revenue chart plots exact amounts paid and does not confuse with service profit', function () {
    $this->actingAs($this->admin);

    $customerGrace = Customer::factory()->create(['name' => 'Grace']);
    $customerTashira = Customer::factory()->create(['name' => 'Tashira']);

    // 01/09: Grace pays $365.00
    Payment::create([
        'customer_id' => $customerGrace->id,
        'invoice_total' => 365.00,
        'amount_paid' => 365.00,
        'payment_method' => PaymentMethod::Zelle,
        'paid_at' => Carbon\Carbon::parse('2026-09-01 10:00:00'),
        'created_at' => Carbon\Carbon::parse('2026-09-01 10:00:00'),
    ]);

    // 02/09: Tashira pays $526.89 for request REQ-0025 (which has service profit $104.48)
    $req = PurchaseRequest::create([
        'customer_id' => $customerTashira->id,
        'product_name' => 'Electrónicos',
        'status' => RequestStatus::Purchased,
        'created_at' => Carbon\Carbon::parse('2026-09-02 12:00:00'),
    ]);
    $req->costItems()->createMany([
        ['type' => CostType::ProductCost, 'description' => 'Producto', 'amount' => 422.41],
        ['type' => CostType::ShopperFee, 'description' => 'Servicio', 'amount' => 104.48],
    ]);

    Payment::create([
        'customer_id' => $customerTashira->id,
        'billable_type' => PurchaseRequest::class,
        'billable_id' => $req->id,
        'invoice_total' => 526.89,
        'amount_paid' => 526.89,
        'payment_method' => PaymentMethod::Zelle,
        'paid_at' => Carbon\Carbon::parse('2026-09-02 14:00:00'),
        'created_at' => Carbon\Carbon::parse('2026-09-02 14:00:00'),
    ]);

    $reports = Livewire::test(ReportsIndex::class)
        ->set('period', 'monthly')
        ->set('month', '2026-09');

    $reportPeriod = $reports->viewData('reportPeriod');
    $revenue = $reportPeriod['revenue'];

    expect($reportPeriod['collected'])->toBe(891.89);

    // Verify revenue buckets sum exactly to 891.89 and contain 365.00 and 526.89
    $day1 = collect($revenue)->first(fn ($r) => str_contains($r['label'], '01'));
    $day2 = collect($revenue)->first(fn ($r) => str_contains($r['label'], '02'));

    expect($day1['total'])->toBe(365.00)
        ->and($day2['total'])->toBe(526.89);
});
