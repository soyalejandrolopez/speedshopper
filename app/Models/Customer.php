<?php

namespace App\Models;

use App\Concerns\GeneratesNumbers;
use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use GeneratesNumbers, HasFactory, SoftDeletes;

    public const PREFIX = 'CUST';

    protected $fillable = [
        'number',
        'user_id',
        'name',
        'email',
        'phone',
        'whatsapp',
        'address',
        'city',
        'country',
        'notes',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'date',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (Customer $customer) {
            $customer->number ??= static::nextNumber(self::PREFIX);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceDueAttribute(): float
    {
        $purchasedStatuses = [
            RequestStatus::Purchased->value,
            RequestStatus::InTransit->value,
            RequestStatus::Received->value,
            RequestStatus::Packing->value,
            RequestStatus::Ready->value,
            RequestStatus::Shipped->value,
            RequestStatus::Delivered->value,
        ];

        // 1. Requests NOT marked as purchased contribute to pending balance
        $unsettledRequests = $this->purchaseRequests()
            ->where('status', '!=', RequestStatus::Cancelled->value)
            ->whereNotIn('status', $purchasedStatuses)
            ->with(['costItems', 'payments'])
            ->get();

        $requestsBalance = (float) $unsettledRequests->sum(function (PurchaseRequest $r) {
            $paid = (float) $r->payments->sum('amount_paid');

            return max(0.0, (float) $r->total_cost - $paid);
        });

        // 2. Shipments: shipping_cost minus payments linked to shipments
        $shipmentsInvoiced = (float) $this->shipments()->sum('shipping_cost');

        $shipmentsPaid = (float) Payment::where('customer_id', $this->id)
            ->where('billable_type', Shipment::class)
            ->sum('amount_paid');

        $shipmentsBalance = max(0.0, $shipmentsInvoiced - $shipmentsPaid);

        // 3. Standalone payments
        $unlinkedPaymentsBalance = (float) $this->payments()
            ->where(function ($q) {
                $q->whereNull('billable_type')->orWhere('billable_type', '');
            })
            ->get()
            ->sum(fn (Payment $p) => (float) $p->balance_due);

        return max(0.0, $requestsBalance + $shipmentsBalance + $unlinkedPaymentsBalance);
    }
}
