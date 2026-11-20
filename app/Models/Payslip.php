<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['payroll_item_id', 'slip_number', 'issued_at', 'file_id'])]
class Payslip extends Model
{
    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function payrollItem(): BelongsTo { return $this->belongsTo(PayrollItem::class); }
    public function file(): BelongsTo { return $this->belongsTo(File::class); }
}
