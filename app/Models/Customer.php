<?php

namespace App\Models;

use App\Concerns\GeneratesNumbers;
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
        $hasActiveRequests = $this->purchaseRequests()
            ->where('status', '!=', \App\Enums\RequestStatus::Cancelled->value)
            ->exists();

        if ($hasActiveRequests || $this->shipments()->where('status', '!=', 'cancelled')->exists()) {
            $requestsInvoiced = (float) $this->purchaseRequests()
                ->where('status', '!=', \App\Enums\RequestStatus::Cancelled->value)
                ->with('costItems')
                ->get()
                ->sum(fn (PurchaseRequest $r) => (float) $r->total_cost);

            $shipmentsInvoiced = (float) $this->shipments()
                ->where('status', '!=', 'cancelled')
                ->with('costItems')
                ->get()
                ->sum(fn (Shipment $s) => (float) $s->total_cost);

            $paid = (float) $this->payments()->sum('amount_paid');

            return max(0.0, ($requestsInvoiced + $shipmentsInvoiced) - $paid);
        }

        return (float) $this->payments()
            ->selectRaw('COALESCE(SUM(invoice_total - amount_paid), 0) as balance')
            ->value('balance');
    }
}
