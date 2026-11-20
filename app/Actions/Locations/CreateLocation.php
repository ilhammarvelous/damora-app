<?php

namespace App\Actions\Locations;

use App\Enums\AuditAction;
use App\Models\Location;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

final class CreateLocation
{
    public function __construct(private readonly AuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data  hasil validated()
     */
    public function execute(User $actor, array $data): Location
    {
        return DB::transaction(function () use ($actor, $data) {
            $location = new Location();
            $location->fill($data);
            $location->forceFill([
                'company_id' => $actor->company_id, // selalu dari pengguna yang login
                'is_active' => true,
            ])->save();

            $this->audit->record(
                $actor,
                AuditAction::CREATE,
                $location,
                new: $location->only(['name', 'address', 'latitude', 'longitude', 'radius_m', 'is_active']),
            );

            return $location;
        });
    }
}
