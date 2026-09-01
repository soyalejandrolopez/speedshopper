<?php

namespace App\Models;

use App\Concerns\GeneratesNumbers;
use App\Enums\CostType;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use GeneratesNumbers, HasFactory, SoftDeletes;

    public const PREFIX = 'PAY';

    protected $fillable = [
        'number',
        'customer_id',
        'billable_id',
        'billable_type',
        'reference',
        'invoice_total',
        'amount_paid',
        'payment_method',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'invoice_total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'paid_at' => 'datetime',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (Payment $payment) {
            $payment->number ??= static::nextNumber(self::PREFIX);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    public function resolveBillable()
    {
        if ($this->relationLoaded('billable') && $this->billable) {
            return $this->billable;
        }

        if ($this->billable_type && $this->billable_id) {
            $type = $this->billable_type;
            if (! str_contains($type, '\\')) {
                $type = 'App\\Models\\'.$type;
            }
            if (class_exists($type)) {
                return $type::find($this->billable_id);
            }
        }

        $search = trim(($this->notes ?? '').' '.($this->reference ?? ''));
        if ($search) {
            if (preg_match('/REQ-[0-9]+/i', $search, $matches)) {
                $req = PurchaseRequest::where('number', strtoupper($matches[0]))->first();
                if ($req) {
                    return $req;
                }
            }
            if (preg_match('/BOX-[0-9]+/i', $search, $matches)) {
                $shipment = Shipment::where('number', strtoupper($matches[0]))->first();
                if ($shipment) {
                    return $shipment;
                }
            }
        }

        if ($this->customer_id) {
            $customerRequests = PurchaseRequest::with('costItems')
                ->where('customer_id', $this->customer_id)
                ->get();
            if ($customerRequests->count() === 1) {
                return $customerRequests->first();
            }

            $customerShipments = Shipment::with('costItems')
                ->where('customer_id', $this->customer_id)
                ->get();
            if ($customerShipments->count() === 1) {
                return $customerShipments->first();
            }
        }

        return null;
    }

    public function getBalanceDueAttribute(): float
    {
        return (float) ($this->invoice_total - $this->amount_paid);
    }

    public function getInvoicedServiceEarningsAttribute(): float
    {
        $billable = $this->resolveBillable();

        if ($billable) {
            if ($billable instanceof PurchaseRequest) {
                $billable->loadMissing('costItems');
                $costItems = $billable->costItems;
                if ($costItems->isNotEmpty()) {
                    return (float) $costItems->where('type', '!=', CostType::ProductCost)->sum('amount');
                }

                $productCost = (float) ($billable->unit_price * max(1, $billable->quantity));
                $invoiceTotal = (float) $this->invoice_total;
                if ($invoiceTotal > $productCost && $productCost > 0) {
                    return (float) ($invoiceTotal - $productCost);
                }
                if ($productCost > 0 && $invoiceTotal <= $productCost) {
                    return 0.0;
                }
            }

            if ($billable instanceof Shipment) {
                $billable->loadMissing('costItems');
                $costItems = $billable->costItems;
                if ($costItems->isNotEmpty()) {
                    return (float) $costItems->where('type', '!=', CostType::ProductCost)->sum('amount');
                }
                $totalCost = (float) $billable->total_cost;
                $shippingCost = (float) $billable->shipping_cost;
                if ($totalCost > $shippingCost && $shippingCost > 0) {
                    return (float) ($totalCost - $shippingCost);
                }
            }
        }

        if ($this->customer_id) {
            $matchedReq = PurchaseRequest::with('costItems')
                ->where('customer_id', $this->customer_id)
                ->latest()
                ->first();
            if ($matchedReq) {
                $costItems = $matchedReq->costItems;
                if ($costItems->isNotEmpty()) {
                    return (float) $costItems->where('type', '!=', CostType::ProductCost)->sum('amount');
                }
            }
        }

        return (float) $this->invoice_total;
    }

    public function getServiceEarningsAttribute(): float
    {
        $invoicedService = (float) $this->invoiced_service_earnings;
        $totalInvoiced = (float) $this->invoice_total;
        $amountPaid = (float) $this->amount_paid;

        if ($totalInvoiced > 0 && $invoicedService > 0) {
            $ratio = min(1.0, $amountPaid / $totalInvoiced);

            return (float) ($invoicedService * $ratio);
        }

        if ($invoicedService <= 0) {
            return 0.0;
        }

        return min($amountPaid, $invoicedService);
    }

    public function getServiceBalanceDueAttribute(): float
    {
        return max(0.0, (float) ($this->invoiced_service_earnings - $this->service_earnings));
    }
}
