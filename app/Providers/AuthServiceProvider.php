<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PurchaseRequest;
use App\Models\Shipment;
use App\Policies\CustomerPolicy;
use App\Policies\PackagePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PurchaseRequestPolicy;
use App\Policies\ShipmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(PurchaseRequest::class, PurchaseRequestPolicy::class);
        Gate::policy(Package::class, PackagePolicy::class);
        Gate::policy(Shipment::class, ShipmentPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
    }
}
