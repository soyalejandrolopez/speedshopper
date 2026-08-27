<?php

use App\Enums\PackageStatus;
use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;

test('numbers are generated with prefix and padding', function () {
    $c1 = Customer::factory()->create();
    $c2 = Customer::factory()->create();
    $c3 = Customer::factory()->create();

    expect($c1->number)->toBe('CUST-0001')
        ->and($c2->number)->toBe('CUST-0002')
        ->and($c3->number)->toBe('CUST-0003');
});

test('request statuses follow the workflow transitions', function () {
    expect(RequestStatus::New->nextStatuses())->toBe([RequestStatus::Quoted->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::Quoted->nextStatuses())->toBe([RequestStatus::AwaitingPayment->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::AwaitingPayment->nextStatuses())->toBe([RequestStatus::Purchased->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::Purchased->nextStatuses())->toBe([RequestStatus::InTransit->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::InTransit->nextStatuses())->toBe([RequestStatus::Received->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::Received->nextStatuses())->toBe([RequestStatus::Packing->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::Packing->nextStatuses())->toBe([RequestStatus::Ready->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::Ready->nextStatuses())->toBe([RequestStatus::Shipped->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::Shipped->nextStatuses())->toBe([RequestStatus::Delivered->value, RequestStatus::Cancelled->value])
        ->and(RequestStatus::Delivered->nextStatuses())->toBe([]);
});

test('package statuses follow the workflow transitions', function () {
    expect(PackageStatus::Received->nextStatuses())->toBe([PackageStatus::Storing->value])
        ->and(PackageStatus::Storing->nextStatuses())->toBe([PackageStatus::Packing->value])
        ->and(PackageStatus::Packing->nextStatuses())->toBe([PackageStatus::Ready->value])
        ->and(PackageStatus::Ready->nextStatuses())->toBe([PackageStatus::Shipped->value])
        ->and(PackageStatus::Shipped->nextStatuses())->toBe([PackageStatus::Delivered->value])
        ->and(PackageStatus::Delivered->nextStatuses())->toBe([]);
});

test('shipment statuses follow the workflow transitions', function () {
    expect(ShipmentStatus::Draft->nextStatuses())->toBe([ShipmentStatus::Ready->value])
        ->and(ShipmentStatus::Ready->nextStatuses())->toBe([ShipmentStatus::InTransit->value])
        ->and(ShipmentStatus::InTransit->nextStatuses())->toBe([ShipmentStatus::Delivered->value])
        ->and(ShipmentStatus::Delivered->nextStatuses())->toBe([]);
});

test('cost totals are computed', function () {
    $customer = Customer::factory()->create();
    $request = PurchaseRequest::factory()->create(['customer_id' => $customer->id]);

    $request->costItems()->createMany([
        ['type' => 'product_cost', 'description' => 'Producto', 'amount' => 69.99],
        ['type' => 'sales_tax', 'description' => 'Tax', 'amount' => 5.77],
        ['type' => 'shopper_fee', 'description' => 'Fee', 'amount' => 10.00],
    ]);

    expect(round($request->total_cost, 2))->toBe(85.76);
});

test('status history records transitions', function () {
    $customer = Customer::factory()->create();
    $package = Package::factory()->create(['customer_id' => $customer->id]);

    $package->transitionTo('storing', 'Registrado en bodega');

    expect($package->status)->toBe(PackageStatus::Storing)
        ->and($package->statusHistory()->count())->toBe(1)
        ->and($package->statusHistory()->first()->from)->toBe('received');
});

test('customer balance due sums open invoices', function () {
    $customer = Customer::factory()->create();

    Payment::factory()->create(['customer_id' => $customer->id, 'invoice_total' => 100, 'amount_paid' => 60]);
    Payment::factory()->create(['customer_id' => $customer->id, 'invoice_total' => 50, 'amount_paid' => 50]);

    expect($customer->balance_due)->toBe(40.0);
});
