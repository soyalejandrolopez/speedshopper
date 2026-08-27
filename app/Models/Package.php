<?php

namespace App\Models;

use App\Concerns\GeneratesNumbers;
use App\Concerns\TracksStatuses;
use App\Enums\PackageStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use GeneratesNumbers, HasFactory, SoftDeletes, TracksStatuses;

    public const PREFIX = 'PKG';

    protected $fillable = [
        'number',
        'customer_id',
        'purchase_request_id',
        'store',
        'original_tracking',
        'received_at',
        'weight_lb',
        'photo_path',
        'location',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PackageStatus::class,
            'received_at' => 'date',
            'weight_lb' => 'decimal:2',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (Package $package) {
            $package->number ??= static::nextNumber(self::PREFIX);
            $package->status ??= PackageStatus::Received;
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class);
    }
}
