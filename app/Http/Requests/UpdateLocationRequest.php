<?php

namespace App\Http\Requests;

class UpdateLocationRequest extends LocationRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('location'));
    }
}
