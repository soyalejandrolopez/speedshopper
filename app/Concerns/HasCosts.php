<?php

namespace App\Concerns;

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
}
