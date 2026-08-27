<?php

namespace App\Livewire\Portal;

use App\Models\Package;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('My Account')]
class PortalDashboard extends Component
{
    public function render()
    {
        $customer = auth()->user()->customer;

        return view('livewire.portal.portal-dashboard', [
            'customer' => $customer,
            'balanceDue' => $customer ? $customer->balance_due : 0.0,
            'requests' => $customer
                ? $customer->purchaseRequests()->latest()->limit(6)->get()
                : collect(),
            'packages' => $customer
                ? $customer->packages()->latest()->limit(4)->get()
                : collect(),
            'shipments' => $customer
                ? $customer->shipments()->withCount('packages')->latest()->limit(4)->get()
                : collect(),
            'openRequestsCount' => $customer
                ? PurchaseRequest::where('customer_id', $customer->id)
                    ->whereNotIn('status', ['delivered', 'cancelled'])
                    ->count()
                : 0,
            'inTransitCount' => $customer
                ? Shipment::where('customer_id', $customer->id)->where('status', 'in_transit')->count()
                : 0,
            'totalPackages' => $customer ? Package::where('customer_id', $customer->id)->count() : 0,
        ]);
    }
}
