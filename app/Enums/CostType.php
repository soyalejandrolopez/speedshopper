<?php

namespace App\Enums;

enum CostType: string
{
    case ProductCost = 'product_cost';
    case SalesTax = 'sales_tax';
    case UsShipping = 'us_shipping';
    case ShopperFee = 'shopper_fee';
    case ReceivingFee = 'receiving_fee';
    case PackingFee = 'packing_fee';
    case InternationalShipping = 'international_shipping';
    case Other = 'other';

    public function label(): string
    {
        return __('enums.cost_type.'.$this->value);
    }

    /** @return list<self> */
    public static function forRequests(): array
    {
        return [self::ProductCost, self::SalesTax, self::UsShipping, self::ShopperFee, self::Other];
    }

    /** @return list<self> */
    public static function forShipments(): array
    {
        return [self::InternationalShipping, self::PackingFee, self::ReceivingFee, self::Other];
    }
}
