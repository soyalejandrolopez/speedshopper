<?php

return [
    'request_status' => [
        'new' => 'Solicitud recibida',
        'quoted' => 'Cotización enviada',
        'awaiting_payment' => 'Esperando pago',
        'purchased' => 'Comprado',
        'in_transit' => 'En tránsito a Baytown',
        'received' => 'Recibido en Baytown',
        'packing' => 'Empacando',
        'ready' => 'Listo para envío',
        'shipped' => 'Enviado',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
    ],
    'package_status' => [
        'received' => 'Recibido',
        'storing' => 'En bodega',
        'packing' => 'Empacando',
        'ready' => 'Listo para envío',
        'shipped' => 'Enviado',
        'delivered' => 'Entregado',
    ],
    'shipment_status' => [
        'draft' => 'Borrador',
        'ready' => 'Listo para envío',
        'in_transit' => 'En tránsito',
        'delivered' => 'Entregado',
    ],
    'cost_type' => [
        'product_cost' => 'Costo del producto',
        'sales_tax' => 'Sales tax',
        'us_shipping' => 'Envío dentro de USA',
        'shopper_fee' => 'Fee personal shopper',
        'receiving_fee' => 'Fee de recepción',
        'packing_fee' => 'Fee de empaque',
        'international_shipping' => 'Envío internacional',
        'other' => 'Otro',
    ],
    'payment_method' => [
        'cash' => 'Efectivo',
        'zelle' => 'Zelle',
        'card' => 'Tarjeta',
        'paypal' => 'PayPal',
        'bank_transfer' => 'Transferencia',
        'other' => 'Otro',
    ],
];
