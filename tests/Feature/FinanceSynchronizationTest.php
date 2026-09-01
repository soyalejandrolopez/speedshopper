<?php

use App\Enums\CostType;
use App\Enums\PaymentMethod;
use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;
use App\Livewire\Admin\Billing\BillingIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Payments\PaymentsIndex;
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
