<?php

namespace App\Services;

use App\Enums\CostType;
use App\Enums\RequestStatus;
use App\Models\CostItem;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;

class FinanceService
{
    /**
     * Get the total invoiced amount across the platform.
     * Includes all non-cancelled purchase requests, non-cancelled shipments,
     * and standalone payments not linked to requests or shipments.
     */
    public function getTotalInvoiced(): float
    {
        $requestsTotal = (float) PurchaseRequest::where('status', '!=', RequestStatus::Cancelled->value)
            ->with('costItems')
            ->get()
            ->sum(fn (PurchaseRequest $r) => (float) $r->total_cost);

        $shipmentsTotal = (float) Shipment::where('status', '!=', 'cancelled')
            ->with('costItems')
            ->get()
            ->sum(fn (Shipment $s) => (float) $s->total_cost);

        $unlinkedPaymentsInvoiced = (float) Payment::where(function ($q) {
            $q->whereNull('billable_type')->orWhere('billable_type', '');
        })->sum('invoice_total');

        return (float) ($requestsTotal + $shipmentsTotal + $unlinkedPaymentsInvoiced);
    }

    /**
     * Get the total money collected / paid across all payments.
     */
    public function getTotalCollected(): float
    {
        $requestsCollected = $this->getRequestsTotalCollected();
        $standalonePayments = (float) Payment::where(function ($q) {
            $q->whereNull('billable_type')->orWhere('billable_type', '!=', PurchaseRequest::class);
        })->sum('amount_paid');

        return (float) ($requestsCollected + $standalonePayments);
    }

    /**
     * Get the total outstanding balance across all customers.
     */
    public function getTotalBalanceDue(): float
    {
        return (float) Customer::all()->sum('balance_due');
    }

    /**
     * Get total service earnings across all requests, shipments, and standalone invoices.
     */
    public function getTotalServiceEarnings(): float
    {
        $requestEarnings = (float) CostItem::where('costable_type', PurchaseRequest::class)
            ->whereIn('costable_id', PurchaseRequest::where('status', '!=', RequestStatus::Cancelled->value)->pluck('id'))
            ->where('type', '!=', CostType::ProductCost)
            ->sum('amount');

        $shipmentEarnings = (float) CostItem::where('costable_type', Shipment::class)
            ->whereIn('costable_id', Shipment::where('status', '!=', 'cancelled')->pluck('id'))
            ->where('type', '!=', CostType::ProductCost)
            ->sum('amount');

        $unlinkedEarnings = (float) Payment::where(function ($q) {
            $q->whereNull('billable_type')->orWhere('billable_type', '');
        })->get()->sum(fn (Payment $p) => (float) $p->invoiced_service_earnings);

        return (float) ($requestEarnings + $shipmentEarnings + $unlinkedEarnings);
    }

    /**
     * Get a unified array of financial metrics.
     *
     * @return array{
     *     total_invoiced: float,
     *     total_collected: float,
     *     total_paid: float,
     *     total_balance_due: float,
     *     total_pending: float,
     *     total_earnings: float
     * }
     */
    public function getMetrics(): array
    {
        $totalInvoiced = $this->getTotalInvoiced();
        $totalCollected = $this->getTotalCollected();
        $totalBalanceDue = $this->getTotalBalanceDue();
        $totalServiceEarnings = $this->getTotalServiceEarnings();

        return [
            'total_invoiced' => $totalInvoiced,
            'total_collected' => $totalCollected,
            'total_paid' => $totalCollected,
            'total_balance_due' => $totalBalanceDue,
            'total_pending' => $totalBalanceDue,
            'total_earnings' => $totalServiceEarnings,
        ];
    }

    /**
     * Get the total invoiced amount for PurchaseRequests only.
     */
    public function getRequestsTotalInvoiced(): float
    {
        return (float) PurchaseRequest::where('status', '!=', RequestStatus::Cancelled->value)
            ->with('costItems')
            ->get()
            ->sum(fn (PurchaseRequest $r) => (float) $r->total_cost);
    }

    /**
     * Get the total service earnings for PurchaseRequests only.
     */
    public function getRequestsTotalEarnings(): float
    {
        return (float) CostItem::where('costable_type', PurchaseRequest::class)
            ->whereIn('costable_id', PurchaseRequest::where('status', '!=', RequestStatus::Cancelled->value)->pluck('id'))
            ->where('type', '!=', CostType::ProductCost)
            ->sum('amount');
    }

    /**
     * Get the total collected / paid for PurchaseRequests only.
     */
    public function getRequestsTotalCollected(): float
    {
        return (float) PurchaseRequest::where('status', '!=', RequestStatus::Cancelled->value)
            ->with(['costItems', 'payments'])
            ->get()
            ->sum(function (PurchaseRequest $r) {
                $registeredPaid = (float) $r->payments->sum('amount_paid');
                $isPurchased = in_array($r->status?->value ?? $r->status, [
                    RequestStatus::Purchased->value,
                    RequestStatus::InTransit->value,
                    RequestStatus::Received->value,
                    RequestStatus::Packing->value,
                    RequestStatus::Ready->value,
                    RequestStatus::Shipped->value,
                    RequestStatus::Delivered->value,
                ], true);

                return $isPurchased ? max($registeredPaid, (float) $r->total_cost) : $registeredPaid;
            });
    }

    /**
     * Get the total outstanding balance for PurchaseRequests only.
     */
    public function getRequestsTotalBalanceDue(): float
    {
        return (float) PurchaseRequest::where('status', '!=', RequestStatus::Cancelled->value)
            ->with(['costItems', 'payments'])
            ->get()
            ->sum(function (PurchaseRequest $r) {
                $isPurchased = in_array($r->status?->value ?? $r->status, [
                    RequestStatus::Purchased->value,
                    RequestStatus::InTransit->value,
                    RequestStatus::Received->value,
                    RequestStatus::Packing->value,
                    RequestStatus::Ready->value,
                    RequestStatus::Shipped->value,
                    RequestStatus::Delivered->value,
                ], true);

                if ($isPurchased) {
                    return 0.0;
                }

                $paid = (float) $r->payments->sum('amount_paid');

                return max(0.0, (float) $r->total_cost - $paid);
            });
    }
}
