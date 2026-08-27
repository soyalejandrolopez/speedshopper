<?php

namespace App\Policies;

use App\Models\PurchaseRequest;
use App\Models\User;

class PurchaseRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isClient();
    }

    public function view(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->isAdmin() || $purchaseRequest->customer_id === $user->customer?->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isClient();
    }

    public function update(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return $user->isAdmin();
    }
}
