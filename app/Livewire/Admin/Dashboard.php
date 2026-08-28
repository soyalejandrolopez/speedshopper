<?php

namespace App\Livewire\Admin;

use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;
use App\Models\ContactInquiry;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public ?int $viewingInquiryId = null;

    public function markInquiryRead(int $id): void
    {
        $inquiry = ContactInquiry::find($id);
        if ($inquiry) {
            $inquiry->markAsRead();
        }
    }

    public function deleteInquiry(int $id): void
    {
        $inquiry = ContactInquiry::find($id);
        if ($inquiry) {
            $inquiry->delete();
        }
    }

    public function render()
    {
        $requestsByStatus = PurchaseRequest::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->pluck('total', 'status')
            ->mapWithKeys(fn ($total, $status) => [RequestStatus::from($status)->label() => $total]);

        $carriers = Shipment::query()
            ->selectRaw('COALESCE(NULLIF(carrier, \'\'), \'—\') as carrier, COUNT(*) as total')
            ->groupBy('carrier')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'carrier');

        return view('livewire.admin.dashboard', [
            'totalCustomers' => Customer::count(),
            'openRequests' => PurchaseRequest::whereNotIn('status', [
                RequestStatus::Delivered->value,
                RequestStatus::Cancelled->value,
            ])->count(),
            'packagesReceivedToday' => Package::whereDate('received_at', today())->count(),
            'storedPackages' => Package::whereIn('status', ['received', 'storing', 'packing', 'ready'])->count(),
            'shipmentsInTransit' => Shipment::where('status', ShipmentStatus::InTransit->value)->count(),
            'readyShipments' => Shipment::where('status', ShipmentStatus::Ready->value)->count(),
            'totalBalanceDue' => (float) Payment::sum('invoice_total') - (float) Payment::sum('amount_paid'),
            'unreadInquiriesCount' => ContactInquiry::unread()->count(),
            'recentInquiries' => ContactInquiry::latest()->limit(6)->get(),
            'recentRequests' => PurchaseRequest::with('customer')->latest()->limit(5)->get(),
            'recentPackages' => Package::with('customer')->latest()->limit(5)->get(),
            'recentPayments' => Payment::with('customer')->latest()->limit(5)->get(),
            'requestsByStatus' => $requestsByStatus,
            'carriers' => $carriers,
            'maxRequests' => max($requestsByStatus->max() ?? 1, 1),
            'maxCarrier' => max($carriers->max() ?? 1, 1),
        ]);
    }
}
