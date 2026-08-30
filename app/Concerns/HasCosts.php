<?php

namespace App\Concerns;

use App\Enums\CostType;
use App\Models\CostItem;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCosts
{
    public function costItems(): MorphMany
    {
        return $this->morphMany(CostItem::class, 'costable');
    }

    public function getTotalCostAttribute(): float
    {
        $sum = (float) ($this->relationLoaded('costItems')
            ? $this->costItems->sum('amount')
            : $this->costItems()->sum('amount'));

        if ($sum > 0) {
            return $sum;
        }

        if (isset($this->unit_price) && $this->unit_price !== null && (float) $this->unit_price > 0) {
            $qty = isset($this->quantity) && $this->quantity > 0 ? (int) $this->quantity : 1;

            return (float) ($this->unit_price * $qty);
        }

        if (isset($this->shipping_cost) && $this->shipping_cost !== null && (float) $this->shipping_cost > 0) {
            return (float) $this->shipping_cost;
        }

        return 0.0;
    }

    public function getServiceEarningsAttribute(): float
    {
        return (float) ($this->relationLoaded('costItems')
            ? $this->costItems->where('type', '!=', CostType::ProductCost)->sum('amount')
            : $this->costItems()->where('type', '!=', CostType::ProductCost)->sum('amount'));
    }

    public function getProductCostAttribute(): float
    {
        return (float) ($this->relationLoaded('costItems')
            ? $this->costItems->where('type', CostType::ProductCost)->sum('amount')
            : $this->costItems()->where('type', CostType::ProductCost)->sum('amount'));
    }
}
