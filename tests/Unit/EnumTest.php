<?php

use App\Enums\CostType;
use App\Enums\PackageStatus;
use App\Enums\PaymentMethod;
use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;

test('all request statuses return a non-empty label', function () {
    foreach (RequestStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty();
    }
});

test('all request statuses return a color', function () {
    foreach (RequestStatus::cases() as $status) {
        expect($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('delivered request status has no next statuses and cancelled can transition to all active statuses', function () {
    expect(RequestStatus::Delivered->nextStatuses())->toBe([])
        ->and(RequestStatus::Cancelled->nextStatuses())->not->toBeEmpty();
});

test('all package statuses return a non-empty label', function () {
    foreach (PackageStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty();
    }
});

test('all package statuses return a color', function () {
    foreach (PackageStatus::cases() as $status) {
        expect($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('delivered package status has no next statuses', function () {
    expect(PackageStatus::Delivered->nextStatuses())->toBe([]);
});

test('all shipment statuses return a non-empty label', function () {
    foreach (ShipmentStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty();
    }
});

test('all shipment statuses return a color', function () {
    foreach (ShipmentStatus::cases() as $status) {
        expect($status->color())->toBeString()->not->toBeEmpty();
    }
});

test('delivered shipment status has no next statuses', function () {
    expect(ShipmentStatus::Delivered->nextStatuses())->toBe([]);
});

test('all payment methods return a non-empty label', function () {
    foreach (PaymentMethod::cases() as $method) {
        expect($method->label())->toBeString()->not->toBeEmpty();
    }
});

test('cost type for requests returns expected types', function () {
    $types = CostType::forRequests();

    expect($types)->toContain(CostType::ProductCost)
        ->toContain(CostType::SalesTax)
        ->toContain(CostType::UsShipping)
        ->toContain(CostType::ShopperFee)
        ->toContain(CostType::Other)
        ->not->toContain(CostType::InternationalShipping)
        ->not->toContain(CostType::PackingFee)
        ->not->toContain(CostType::ReceivingFee);
});

test('cost type for shipments returns expected types', function () {
    $types = CostType::forShipments();

    expect($types)->toContain(CostType::InternationalShipping)
        ->toContain(CostType::PackingFee)
        ->toContain(CostType::ReceivingFee)
        ->toContain(CostType::Other)
        ->not->toContain(CostType::ProductCost)
        ->not->toContain(CostType::SalesTax);
});

test('all cost types return a non-empty label', function () {
    foreach (CostType::cases() as $type) {
        expect($type->label())->toBeString()->not->toBeEmpty();
    }
});

test('every request status except terminal ones have at least one transition', function () {
    $terminal = [RequestStatus::Delivered];

    foreach (RequestStatus::cases() as $status) {
        if (in_array($status, $terminal, true)) {
            continue;
        }
        expect($status->nextStatuses())->not->toBeEmpty(
            "Expected {$status->value} to have at least one transition"
        );
    }
});
