<?php

namespace App\Enums;

enum RequestStatus: string
{
    case New = 'new';
    case Quoted = 'quoted';
    case AwaitingPayment = 'awaiting_payment';
    case Purchased = 'purchased';
    case InTransit = 'in_transit';
    case Received = 'received';
    case Packing = 'packing';
    case Ready = 'ready';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __('enums.request_status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'gray',
            self::Quoted => 'blue',
            self::AwaitingPayment => 'amber',
            self::Purchased => 'indigo',
            self::InTransit => 'purple',
            self::Received => 'green',
            self::Packing => 'cyan',
            self::Ready => 'sky',
            self::Shipped => 'indigo',
            self::Delivered => 'green',
            self::Cancelled => 'red',
        };
    }

    /** @return array<string, array<int, string>> */
    public static function transitions(): array
    {
        return [
            self::New->value => [self::Quoted->value, self::Cancelled->value],
            self::Quoted->value => [self::AwaitingPayment->value, self::Cancelled->value],
            self::AwaitingPayment->value => [self::Purchased->value, self::Cancelled->value],
            self::Purchased->value => [self::InTransit->value, self::Cancelled->value],
            self::InTransit->value => [self::Received->value, self::Cancelled->value],
            self::Received->value => [self::Packing->value, self::Cancelled->value],
            self::Packing->value => [self::Ready->value, self::Cancelled->value],
            self::Ready->value => [self::Shipped->value, self::Cancelled->value],
            self::Shipped->value => [self::Delivered->value, self::Cancelled->value],
            self::Delivered->value => [],
            self::Cancelled->value => [
                self::New->value,
                self::Quoted->value,
                self::AwaitingPayment->value,
                self::Purchased->value,
                self::InTransit->value,
                self::Received->value,
                self::Packing->value,
                self::Ready->value,
                self::Shipped->value,
                self::Delivered->value,
            ],
        ];
    }

    /** @return list<string> */
    public function nextStatuses(): array
    {
        return self::transitions()[$this->value];
    }

    public function isBilled(): bool
    {
        return ! in_array($this, [self::New, self::Quoted, self::AwaitingPayment, self::Cancelled]);
    }

    public function isQuote(): bool
    {
        return in_array($this, [self::New, self::Quoted, self::AwaitingPayment]);
    }
}
