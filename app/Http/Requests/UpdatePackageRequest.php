<?php

namespace App\Http\Requests;

class UpdatePackageRequest extends StorePackageRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
