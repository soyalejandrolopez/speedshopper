<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public bool $isOpen = false;

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->query = '';
    }

    public function render()
    {
        $results = [
            'customers' => collect(),
            'requests' => collect(),
            'packages' => collect(),
            'shipments' => collect(),
            'payments' => collect(),
        ];

        $q = trim($this->query);

        if (strlen($q) >= 2) {
            $results['customers'] = Customer::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('number', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('whatsapp', 'like', "%{$q}%");
                })
                ->limit(4)
                ->get();

            $results['requests'] = PurchaseRequest::query()
                ->with('customer')
                ->where(function ($query) use ($q) {
                    $query->where('number', 'like', "%{$q}%")
                        ->orWhere('product_name', 'like', "%{$q}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%"));
                })
                ->limit(4)
                ->get();

            $results['packages'] = Package::query()
                ->with('customer')
                ->where(function ($query) use ($q) {
                    $query->where('number', 'like', "%{$q}%")
                        ->orWhere('tracking_number', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%"));
                })
                ->limit(4)
                ->get();

            $results['shipments'] = Shipment::query()
                ->with('customer')
                ->where(function ($query) use ($q) {
                    $query->where('number', 'like', "%{$q}%")
                        ->orWhere('tracking_number', 'like', "%{$q}%")
                        ->orWhere('carrier', 'like', "%{$q}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%"));
                })
                ->limit(4)
                ->get();

            $results['payments'] = Payment::query()
                ->with('customer')
                ->where(function ($query) use ($q) {
                    $query->where('number', 'like', "%{$q}%")
                        ->orWhere('reference', 'like', "%{$q}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%"));
                })
                ->limit(4)
                ->get();
        }

        $totalResults = collect($results)->sum(fn ($items) => $items->count());

        return view('livewire.admin.global-search', [
            'results' => $results,
            'totalResults' => $totalResults,
        ]);
    }
}
