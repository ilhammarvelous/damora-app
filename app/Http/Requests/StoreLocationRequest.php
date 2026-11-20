<?php

namespace App\Http\Requests;

use App\Models\Location;

class StoreLocationRequest extends LocationRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Location::class);
    }
}
