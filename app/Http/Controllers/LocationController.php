<?php

namespace App\Http\Controllers;

use App\Actions\Locations\CreateLocation;
use App\Actions\Locations\SetLocationActive;
use App\Actions\Locations\UpdateLocation;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UpdateLocationStatusRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Location::class);

        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status');

        $locations = Location::query()
            ->when($search !== '', function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $query->where(fn ($q) => $q->where('name', 'ilike', $like)->orWhere('address', 'ilike', $like));
            })
            ->when(in_array($status, ['aktif', 'nonaktif'], true), fn ($query) => $query->where('is_active', $status === 'aktif'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('locations/index', [
            'locations' => LocationResource::collection($locations),
            'filters' => $request->only(['search', 'status']),
            'defaults' => ['radius_m' => $request->user()->company->initial_geofence_radius_m],
        ]);
    }

    public function store(StoreLocationRequest $request, CreateLocation $action): RedirectResponse
    {
        $action->execute($request->user(), $request->validated());

        return redirect()->route('locations.index')->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }


    public function update(UpdateLocationRequest $request, Location $location, UpdateLocation $action): RedirectResponse
    {
        $action->execute($request->user(), $location, $request->validated());

        return redirect()->route('locations.index')->with('success', 'Lokasi kantor berhasil diperbarui.');
    }

    public function updateStatus(UpdateLocationStatusRequest $request, Location $location, SetLocationActive $action): RedirectResponse
    {
        $active = $request->boolean('is_active');
        $action->execute($request->user(), $location, $active);

        return back()->with('success', $active ? 'Lokasi kantor diaktifkan.' : 'Lokasi kantor dinonaktifkan.');
    }
}
