<?php

namespace App\Enums;

enum PackageStatus: string
{
    case Received = 'received';
    case Storing = 'storing';
    case Packing = 'packing';
    case Ready = 'ready';
    case Shipped = 'shipped';
    case Delivered = 'delivered';

    public function label(): string
    {
        return __('enums.package_status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Received => 'blue',
            self::Storing => 'indigo',
            self::Packing => 'amber',
            self::Ready => 'purple',
            self::Shipped => 'cyan',
            self::Delivered => 'green',
        };
    }

    /** @return array<string, string> */
    public static function transitions(): array
    {
        return [
            self::Received->value => [self::Storing->value],
            self::Storing->value => [self::Packing->value],
            self::Packing->value => [self::Ready->value],
            self::Ready->value => [self::Shipped->value],
            self::Shipped->value => [self::Delivered->value],
            self::Delivered->value => [],
        ];
    }

    /** @return list<string> */
    public function nextStatuses(): array
    {
        return self::transitions()[$this->value];
    }
}
