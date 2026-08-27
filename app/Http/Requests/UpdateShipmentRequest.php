<?php

namespace App\Http\Requests;

class UpdateShipmentRequest extends StoreShipmentRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
