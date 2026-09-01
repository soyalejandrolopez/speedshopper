<?php

namespace App\Livewire\Admin;

use App\Models\ContactInquiry;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';

    public string $category = 'all';

    public bool $isOpen = false;

    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->query = '';
        $this->category = 'all';
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function render(): View
    {
        $results = [
            'customers' => collect(),
            'requests' => collect(),
            'packages' => collect(),
            'shipments' => collect(),
            'payments' => collect(),
            'inquiries' => collect(),
        ];

        $q = trim($this->query);

        if (strlen($q) >= 2) {
            if ($this->category === 'all' || $this->category === 'customers') {
                $results['customers'] = Customer::query()
                    ->where(function ($query) use ($q) {
                        $query->where('name', 'like', "%{$q}%")
                            ->orWhere('number', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%")
                            ->orWhere('whatsapp', 'like', "%{$q}%")
                            ->orWhere('city', 'like', "%{$q}%")
                            ->orWhere('country', 'like', "%{$q}%");
                    })
                    ->latest()
                    ->limit(6)
                    ->get();
            }

            if ($this->category === 'all' || $this->category === 'requests') {
                $results['requests'] = PurchaseRequest::query()
                    ->with('customer')
                    ->where(function ($query) use ($q) {
                        $query->where('number', 'like', "%{$q}%")
                            ->orWhere('product_name', 'like', "%{$q}%")
                            ->orWhere('store', 'like', "%{$q}%")
                            ->orWhere('description', 'like', "%{$q}%")
                            ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%")->orWhere('number', 'like', "%{$q}%"));
                    })
                    ->latest()
                    ->limit(6)
                    ->get();
            }

            if ($this->category === 'all' || $this->category === 'packages') {
                $results['packages'] = Package::query()
                    ->with('customer')
                    ->where(function ($query) use ($q) {
                        $query->where('number', 'like', "%{$q}%")
                            ->orWhere('original_tracking', 'like', "%{$q}%")
                            ->orWhere('store', 'like', "%{$q}%")
                            ->orWhere('location', 'like', "%{$q}%")
                            ->orWhere('notes', 'like', "%{$q}%")
                            ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%")->orWhere('number', 'like', "%{$q}%"));
                    })
                    ->latest()
                    ->limit(6)
                    ->get();
            }

            if ($this->category === 'all' || $this->category === 'shipments') {
                $results['shipments'] = Shipment::query()
                    ->with('customer')
                    ->where(function ($query) use ($q) {
                        $query->where('number', 'like', "%{$q}%")
                            ->orWhere('tracking_number', 'like', "%{$q}%")
                            ->orWhere('carrier', 'like', "%{$q}%")
                            ->orWhere('destination_address', 'like', "%{$q}%")
                            ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%")->orWhere('number', 'like', "%{$q}%"));
                    })
                    ->latest()
                    ->limit(6)
                    ->get();
            }

            if ($this->category === 'all' || $this->category === 'payments') {
                $results['payments'] = Payment::query()
                    ->with('customer')
                    ->where(function ($query) use ($q) {
                        $query->where('number', 'like', "%{$q}%")
                            ->orWhere('reference', 'like', "%{$q}%")
                            ->orWhere('payment_method', 'like', "%{$q}%")
                            ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%")->orWhere('number', 'like', "%{$q}%"));
                    })
                    ->latest()
                    ->limit(6)
                    ->get();
            }

            if ($this->category === 'all' || $this->category === 'inquiries') {
                $results['inquiries'] = ContactInquiry::query()
                    ->where(function ($query) use ($q) {
                        $query->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%")
                            ->orWhere('subject', 'like', "%{$q}%")
                            ->orWhere('message', 'like', "%{$q}%");
                    })
                    ->latest()
                    ->limit(6)
                    ->get();
            }
        }

        $counts = [
            'customers' => $results['customers']->count(),
            'requests' => $results['requests']->count(),
            'packages' => $results['packages']->count(),
            'shipments' => $results['shipments']->count(),
            'payments' => $results['payments']->count(),
            'inquiries' => $results['inquiries']->count(),
        ];

        $totalResults = array_sum($counts);

        return view('livewire.admin.global-search', [
            'results' => $results,
            'counts' => $counts,
            'totalResults' => $totalResults,
        ]);
    }
}
