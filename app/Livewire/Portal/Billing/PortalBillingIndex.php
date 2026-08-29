<?php

namespace App\Livewire\Portal\Billing;

use App\Services\PricingRateService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Facturación y Tarifas')]
class PortalBillingIndex extends Component
{
    public function render()
    {
        $customer = auth()->user()->customer;
        $rateService = app(PricingRateService::class);
        $rates = $rateService->getRates();

        $quotes = $customer
            ? $customer->purchaseRequests()->with('costItems')->latest()->take(10)->get()
            : collect();

        $shipments = $customer
            ? $customer->shipments()->with('costItems')->latest()->take(10)->get()
            : collect();

        return view('livewire.portal.billing.portal-billing-index', [
            'rates' => $rates,
            'quotes' => $quotes,
            'shipments' => $shipments,
            'customer' => $customer,
        ]);
    }
}
