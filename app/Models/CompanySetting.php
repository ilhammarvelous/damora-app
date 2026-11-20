<?php

namespace App\Models;

use App\Models\Concerns\HasCompositeKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'setting_key', 'setting_value'])]
class CompanySetting extends Model
{
    use HasCompositeKey;

    protected $primaryKey = ['company_id', 'setting_key'];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'setting_value' => 'array',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
