<?php

namespace App\Actions\Locations;

use App\Enums\AuditAction;
use App\Models\Location;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

final class SetLocationActive
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function execute(User $actor, Location $location, bool $active): Location
    {
        if ($location->is_active === $active) {
            return $location;
        }

        return DB::transaction(function () use ($actor, $location, $active) {
            $location->forceFill(['is_active' => $active])->save();

            $this->audit->record(
                $actor,
                $active ? AuditAction::ACTIVATE : AuditAction::DEACTIVATE,
                $location,
                ['is_active' => ! $active],
                ['is_active' => $active],
            );

            return $location;
        });
    }
}
