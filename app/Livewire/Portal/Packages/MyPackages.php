<?php

namespace App\Livewire\Portal\Packages;

use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
#[Title('My Packages')]
class MyPackages extends Component
{
    use WithPagination;

    public function render()
    {
        $customer = auth()->user()->customer;

        $packages = $customer
            ? $customer->packages()->latest()->paginate(10)
            : new LengthAwarePaginator([], 0, 10);

        return view('livewire.portal.packages.my-packages', [
            'packages' => $packages,
        ]);
    }
}
