<?php

namespace App\Models;

use App\Enums\OvertimeRoundingMode;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'company_id', 'rounding_mode', 'rounding_interval_minutes',
    'minimum_minutes', 'effective_from',
])]
class OvertimeRule extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'rounding_mode' => OvertimeRoundingMode::class,
            'rounding_interval_minutes' => 'integer',
            'minimum_minutes' => 'integer',
            'effective_from' => 'date',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function overtimeRecords(): HasMany { return $this->hasMany(OvertimeRecord::class); }
}
