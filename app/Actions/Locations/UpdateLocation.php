<?php

namespace App\Actions\Locations;

use App\Enums\AuditAction;
use App\Models\Location;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

final class UpdateLocation
{
    public function __construct(private readonly AuditLogger $audit) {}

    /**
     * @param  array<string, mixed>  $data  hasil validated()
     */
    public function execute(User $actor, Location $location, array $data): Location
    {
        return DB::transaction(function () use ($actor, $location, $data) {
            $location->fill($data);

            // Tidak ada yang berubah: tidak perlu menyimpan atau mencatat audit
            if (! $location->isDirty()) {
                return $location;
            }

            $new = $location->getDirty();
            $old = Arr::only($location->getOriginal(), array_keys($new));

            $location->save();

            $this->audit->record($actor, AuditAction::UPDATE, $location, $old, $new);

            return $location;
        });
    }
}
