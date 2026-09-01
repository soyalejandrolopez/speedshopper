<?php

namespace App\Livewire\Admin;

use App\Enums\PackageStatus;
use App\Enums\RequestStatus;
use App\Enums\ShipmentStatus;
use App\Models\ContactInquiry;
use App\Models\CostItem;
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

        $costItemsTotal = (float) CostItem::where(function ($q) {
            $q->where('costable_type', PurchaseRequest::class)
                ->whereIn('costable_id', PurchaseRequest::where('status', '!=', RequestStatus::Cancelled->value)->pluck('id'));
        })->orWhere(function ($q) {
            $q->where('costable_type', Shipment::class)
                ->whereIn('costable_id', Shipment::where('status', '!=', 'cancelled')->pluck('id'));
        })->sum('amount');

        $paymentsInvoiced = (float) Payment::sum('invoice_total');
        $totalInvoiced = max($costItemsTotal, $paymentsInvoiced);
        $totalPaid = (float) Payment::sum('amount_paid');

        $customerBalanceSum = (float) Customer::all()->sum('balance_due');
        $totalBalanceDue = $customerBalanceSum > 0 ? $customerBalanceSum : max(0.0, $totalInvoiced - $totalPaid);

        return view('livewire.admin.dashboard', [
            'totalCustomers' => Customer::count(),
            'totalRequests' => PurchaseRequest::count(),
            'openRequests' => PurchaseRequest::whereNotIn('status', [
                RequestStatus::Delivered->value,
                RequestStatus::Cancelled->value,
            ])->count(),
            'totalPackages' => Package::count(),
            'storedPackages' => Package::whereIn('status', [
                PackageStatus::Received->value,
                PackageStatus::Storing->value,
                PackageStatus::Packing->value,
                PackageStatus::Ready->value,
                'received', 'storing', 'packing', 'ready',
            ])->count(),
            'totalShipments' => Shipment::count(),
            'shipmentsInTransit' => Shipment::where('status', ShipmentStatus::InTransit->value)->count(),
            'totalPayments' => Payment::count(),
            'totalPaid' => $totalPaid,
            'totalInvoiced' => $totalInvoiced,
            'totalBalanceDue' => $totalBalanceDue,
            'totalInquiries' => ContactInquiry::count(),
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
