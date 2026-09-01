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
        return (float) Payment::sum('amount_paid');
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
}
