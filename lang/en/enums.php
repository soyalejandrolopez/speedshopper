<?php

return [
    'request_status' => [
        'new' => 'Request received',
        'quoted' => 'Quote sent',
        'awaiting_payment' => 'Awaiting payment',
        'purchased' => 'Purchased',
        'in_transit' => 'In transit to Baytown',
        'received' => 'Received in Baytown',
        'packing' => 'Packing',
        'ready' => 'Ready to ship',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ],
    'package_status' => [
        'received' => 'Received',
        'storing' => 'Stored',
        'packing' => 'Packing',
        'ready' => 'Ready to ship',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
    ],
    'shipment_status' => [
        'draft' => 'Draft',
        'ready' => 'Ready to ship',
        'in_transit' => 'In transit',
        'delivered' => 'Delivered',
    ],
    'cost_type' => [
        'product_cost' => 'Product cost',
        'sales_tax' => 'Sales tax',
        'us_shipping' => 'US shipping',
        'shopper_fee' => 'Personal shopper fee',
        'receiving_fee' => 'Receiving fee',
        'packing_fee' => 'Packing fee',
        'international_shipping' => 'International shipping',
        'other' => 'Other',
    ],
    'payment_method' => [
        'cash' => 'Cash',
        'zelle' => 'Zelle',
        'card' => 'Card',
        'paypal' => 'PayPal',
        'bank_transfer' => 'Bank transfer',
        'other' => 'Other',
    ],
];
