<?php

namespace App\Models;

use App\Concerns\GeneratesNumbers;
use App\Concerns\HasCosts;
use App\Concerns\TracksStatuses;
use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use GeneratesNumbers, HasCosts, HasFactory, SoftDeletes, TracksStatuses;

    public const PREFIX = 'BOX';

    protected $fillable = [
        'number',
        'customer_id',
        'carrier',
        'destination_country',
        'final_weight_lb',
        'dimensions',
        'international_tracking',
        'shipping_cost',
        'shipped_at',
        'delivered_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'final_weight_lb' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'shipped_at' => 'date',
            'delivered_at' => 'date',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (Shipment $shipment) {
            $shipment->number ??= static::nextNumber(self::PREFIX);
            $shipment->status ??= ShipmentStatus::Draft;
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class);
    }
}
