<?php

namespace App\Livewire\Admin\Packages;

use App\Models\Package;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Package Details')]
class PackageShow extends Component
{
    public Package $package;

    public string $newStatus = '';

    public string $transitionNote = '';

    public function mount(Package $package): void
    {
        $this->authorize('view', $package);
    }

    public function transitionStatus(): void
    {
        $this->authorize('update', $this->package);

        $validated = $this->validate([
            'newStatus' => ['required', 'string', 'in:'.implode(',', $this->package->status->nextStatuses())],
            'transitionNote' => ['nullable', 'string', 'max:500'],
        ]);

        $this->package->transitionTo(
            $validated['newStatus'],
            $validated['transitionNote'],
            auth()->user()
        );

        $this->reset('newStatus', 'transitionNote');
        $this->swalUpdated();
    }

    public function render()
    {
        return view('livewire.admin.packages.package-show');
    }
}
