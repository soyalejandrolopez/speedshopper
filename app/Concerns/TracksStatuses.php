<?php

namespace App\Concerns;

use App\Models\StatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait TracksStatuses
{
    public function statusHistory(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'statusable')->latest();
    }

    public function recordStatus(string $to, ?string $note = null, ?User $user = null): void
    {
        $this->statusHistory()->create([
            'from' => $this->status->value,
            'to' => $to,
            'note' => $note,
            'user_id' => $user?->id,
        ]);
    }

    public function transitionTo(string $to, ?string $note = null, ?User $user = null): void
    {
        $from = $this->status->value;

        $this->update(['status' => $to]);

        $this->statusHistory()->create([
            'from' => $from,
            'to' => $to,
            'note' => $note,
            'user_id' => $user?->id,
        ]);

        app(\App\Services\StatusNotifier::class)->notify($this, $from, $to);
    }
}
