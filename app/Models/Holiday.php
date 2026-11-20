<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

#[Fillable(['company_id', 'holiday_date', 'name'])]

class Holiday extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'holiday_date' => 'date',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
