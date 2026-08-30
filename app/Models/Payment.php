<?php

namespace App\Models;

use App\Concerns\GeneratesNumbers;
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

    public function getBalanceDueAttribute(): float
    {
        return (float) ($this->invoice_total - $this->amount_paid);
    }

    public function getServiceEarningsAttribute(): float
    {
        if ($this->billable) {
            if ($this->billable instanceof PurchaseRequest) {
                $serviceProfit = (float) $this->billable->costItems->where('type', '!=', \App\Enums\CostType::ProductCost)->sum('amount');
                $totalCost = (float) $this->billable->total_cost;
                if ($totalCost > 0 && $this->invoice_total > 0) {
                    $ratio = min(1.0, (float) ($this->amount_paid / $this->invoice_total));
                    return (float) ($serviceProfit * $ratio);
                }
                return (float) $serviceProfit;
            }

            if ($this->billable instanceof Shipment) {
                $serviceProfit = (float) $this->billable->costItems->where('type', '!=', \App\Enums\CostType::ProductCost)->sum('amount');
                if ($serviceProfit <= 0 && $this->billable->total_cost > 0) {
                    $serviceProfit = max(0.0, (float) ($this->billable->total_cost - $this->billable->shipping_cost));
                }
                $totalCost = (float) $this->billable->total_cost;
                if ($totalCost > 0 && $this->invoice_total > 0) {
                    $ratio = min(1.0, (float) ($this->amount_paid / $this->invoice_total));
                    return (float) ($serviceProfit * $ratio);
                }
                return (float) $serviceProfit;
            }
        }

        return (float) $this->amount_paid;
    }

    public function getInvoicedServiceEarningsAttribute(): float
    {
        if ($this->billable) {
            if ($this->billable instanceof PurchaseRequest) {
                return (float) $this->billable->costItems->where('type', '!=', \App\Enums\CostType::ProductCost)->sum('amount');
            }

            if ($this->billable instanceof Shipment) {
                $serviceProfit = (float) $this->billable->costItems->where('type', '!=', \App\Enums\CostType::ProductCost)->sum('amount');
                if ($serviceProfit <= 0 && $this->billable->total_cost > 0) {
                    return max(0.0, (float) ($this->billable->total_cost - $this->billable->shipping_cost));
                }
                return (float) $serviceProfit;
            }
        }

        return (float) $this->invoice_total;
    }

    public function getServiceBalanceDueAttribute(): float
    {
        return max(0.0, (float) ($this->invoiced_service_earnings - $this->service_earnings));
    }
}
