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
}
