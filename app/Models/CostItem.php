<?php

namespace App\Models;

use App\Enums\CostType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CostItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'type' => CostType::class,
            'amount' => 'decimal:2',
        ];
    }

    public function costable(): MorphTo
    {
        return $this->morphTo();
    }
}
