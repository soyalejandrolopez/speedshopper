<?php

namespace App\Livewire\Portal\Requests;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Nueva Solicitud de Compra')]
class CreateRequest extends Component
{
    public function render()
    {
        return view('livewire.portal.requests.create-request');
    }
}
