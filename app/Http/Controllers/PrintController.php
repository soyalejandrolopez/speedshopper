<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\Shipment;
use Illuminate\View\View;

class PrintController extends Controller
{
    public function requestQuote(PurchaseRequest $purchaseRequest): View
    {
        $this->authorize('view', $purchaseRequest);

        return view('print.quote', [
            'request' => $purchaseRequest->load(['customer', 'costItems']),
        ]);
    }

    public function shipmentReceipt(Shipment $shipment): View
    {
        $this->authorize('view', $shipment);

        return view('print.receipt', [
            'shipment' => $shipment->load(['customer', 'packages', 'costItems']),
        ]);
    }
}
