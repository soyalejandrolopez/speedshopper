<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Draft = 'draft';
    case Ready = 'ready';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';

    public function label(): string
    {
        return __('enums.shipment_status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Ready => 'amber',
            self::InTransit => 'purple',
            self::Delivered => 'green',
        };
    }

    /** @return array<string, string> */
    public static function transitions(): array
    {
        return [
            self::Draft->value => [self::Ready->value],
            self::Ready->value => [self::InTransit->value],
            self::InTransit->value => [self::Delivered->value],
            self::Delivered->value => [],
        ];
    }

    /** @return list<string> */
    public function nextStatuses(): array
    {
        return self::transitions()[$this->value];
    }
}
