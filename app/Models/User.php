<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

// Jangan mass-assign role/status dari input request.
#[Fillable([
    'company_id', 'role', 'email', 'password_hash',
    'status', 'must_change_password', 'last_login_at',
])]
#[Hidden(['password_hash'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
            'role' => UserRole::class,
            'status' => AccountStatus::class,
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Kolom password bernama password_hash
    public function getAuthPassword(): string { return $this->password_hash; }
    public function getAuthPasswordName(): string { return 'password_hash'; }

    // Tidak ada kolom remember_token di desain DAMORA
    public function getRememberTokenName(): string { return ''; }

    public function isOwner(): bool { return $this->role === UserRole::OWNER; }
    public function isActive(): bool { return $this->status === AccountStatus::ACTIVE; }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function employee(): HasOne { return $this->hasOne(Employee::class); }
    public function notifications(): HasMany { return $this->hasMany(Notification::class); }
    public function actedAuditLogs(): HasMany { return $this->hasMany(AuditLog::class, 'actor_user_id'); }
    // public function passwordResets(): HasMany { return $this->hasMany(PasswordReset::class); }
}
