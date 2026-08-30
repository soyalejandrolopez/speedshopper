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
        return (float) $this->costItems()->sum('amount');
    }

    public function getServiceEarningsAttribute(): float
    {
        return (float) $this->costItems()
            ->where('type', '!=', CostType::ProductCost)
            ->sum('amount');
    }

    public function getProductCostAttribute(): float
    {
        return (float) $this->costItems()
            ->where('type', CostType::ProductCost)
            ->sum('amount');
    }
}
