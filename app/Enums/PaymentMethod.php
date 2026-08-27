<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Zelle = 'zelle';
    case Card = 'card';
    case PayPal = 'paypal';
    case BankTransfer = 'bank_transfer';
    case Other = 'other';

    public function label(): string
    {
        return __('enums.payment_method.'.$this->value);
    }
}
