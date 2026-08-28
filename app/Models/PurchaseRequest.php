<?php

namespace App\Models;

use App\Concerns\GeneratesNumbers;
use App\Concerns\HasCosts;
use App\Concerns\TracksStatuses;
use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use GeneratesNumbers, HasCosts, HasFactory, SoftDeletes, TracksStatuses;

    public const PREFIX = 'REQ';

    protected $fillable = [
        'number',
        'customer_id',
        'product_name',
        'product_url',
        'store',
        'description',
        'size_color',
        'quantity',
        'unit_price',
        'discount_found',
        'status',
        'services',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => RequestStatus::class,
            'services' => 'array',
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount_found' => 'decimal:2',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (PurchaseRequest $request) {
            $request->number ??= static::nextNumber(self::PREFIX);
            $request->status ??= RequestStatus::New;
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
