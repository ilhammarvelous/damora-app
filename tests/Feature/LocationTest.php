<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\CompanyStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    private function createCompany(string $name): Company
    {
        return Company::create([
            'name' => $name,
            'timezone' => 'Asia/Jakarta',
            'initial_geofence_radius_m' => 50,
            'status' => CompanyStatus::ACTIVE,
        ]);
    }

    private function createUser(Company $company, UserRole $role, string $email): User
    {
        $user = new User();
        $user->forceFill([
            'company_id' => $company->id,
            'role' => $role,
            'email' => $email,
            'password_hash' => 'rahasia-uji',
            'status' => AccountStatus::ACTIVE,
            'must_change_password' => false,
        ])->save();

        return $user;
    }

    private function createLocation(Company $company, string $name = 'Kantor Pusat'): Location
    {
        $location = new Location();
        $location->forceFill([
            'company_id' => $company->id,
            'name' => $name,
            'latitude' => -7.257472,
            'longitude' => 112.752088,
            'radius_m' => 50,
            'is_active' => true,
        ])->save();

        return $location;
    }

    /** @return array<string, mixed> */
    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => 'Kantor Pusat',
            'address' => 'Jl. Contoh No. 1, Surabaya',
            'latitude' => -7.257472,
            'longitude' => 112.752088,
            'radius_m' => 50,
        ], $override);
    }

    public function test_owner_can_create_location_and_audit_is_recorded(): void
    {
        $company = $this->createCompany('PT A');
        $owner = $this->createUser($company, UserRole::OWNER, 'owner@a.test');

        $this->actingAs($owner)
            ->post(route('locations.store'), $this->payload())
            ->assertRedirect();

        $this->assertDatabaseHas('locations', ['company_id' => $company->id, 'name' => 'Kantor Pusat', 'is_active' => true]);
        $this->assertDatabaseHas('audit_logs', ['entity_type' => 'Location', 'action' => 'buat', 'actor_user_id' => $owner->id]);
    }

    public function test_company_id_from_input_is_ignored(): void
    {
        $companyA = $this->createCompany('PT A');
        $companyB = $this->createCompany('PT B');
        $owner = $this->createUser($companyA, UserRole::OWNER, 'owner@a.test');

        $this->actingAs($owner)->post(route('locations.store'), $this->payload(['company_id' => $companyB->id]));

        $this->assertDatabaseHas('locations', ['company_id' => $companyA->id]);
        $this->assertDatabaseMissing('locations', ['company_id' => $companyB->id]);
    }

    public function test_employee_cannot_access_locations(): void
    {
        $company = $this->createCompany('PT A');
        $employee = $this->createUser($company, UserRole::EMPLOYEE, 'karyawan@a.test');

        $this->actingAs($employee)->get(route('locations.index'))->assertForbidden();
        $this->actingAs($employee)->post(route('locations.store'), $this->payload())->assertForbidden();
        $this->assertDatabaseCount('locations', 0);
    }

    public function test_guest_is_redirected(): void
    {
        $this->get(route('locations.index'))->assertRedirect();
    }

    public function test_owner_cannot_see_or_change_other_company_location(): void
    {
        $companyA = $this->createCompany('PT A');
        $companyB = $this->createCompany('PT B');
        $ownerB = $this->createUser($companyB, UserRole::OWNER, 'owner@b.test');
        $locationA = $this->createLocation($companyA, 'Kantor A');

        // $this->actingAs($ownerB)->get(route('locations.edit', $locationA))->assertNotFound();
        $this->actingAs($ownerB)->put(route('locations.update', $locationA), $this->payload(['name' => 'Dibajak']))->assertNotFound();
        $this->actingAs($ownerB)->patch(route('locations.status', $locationA), ['is_active' => false])->assertNotFound();

        $this->assertDatabaseHas('locations', ['id' => $locationA->id, 'name' => 'Kantor A', 'is_active' => true]);
    }

    public function test_invalid_coordinates_and_radius_are_rejected(): void
    {
        $company = $this->createCompany('PT A');
        $owner = $this->createUser($company, UserRole::OWNER, 'owner@a.test');

        $this->actingAs($owner)->post(route('locations.store'), $this->payload(['latitude' => 120]))
            ->assertSessionHasErrors('latitude');
        $this->actingAs($owner)->post(route('locations.store'), $this->payload(['radius_m' => 5000]))
            ->assertSessionHasErrors('radius_m');

        $this->assertDatabaseCount('locations', 0);
    }

    public function test_owner_can_update_location(): void
    {
        $company = $this->createCompany('PT A');
        $owner = $this->createUser($company, UserRole::OWNER, 'owner@a.test');
        $location = $this->createLocation($company);

        $this->actingAs($owner)
            ->put(route('locations.update', $location), $this->payload(['name' => 'Kantor Cabang', 'radius_m' => 80]))
            ->assertRedirect();

        $this->assertDatabaseHas('locations', ['id' => $location->id, 'name' => 'Kantor Cabang', 'radius_m' => 80]);
        $this->assertDatabaseHas('audit_logs', ['entity_type' => 'Location', 'entity_id' => $location->id, 'action' => 'ubah']);
    }

    public function test_owner_can_deactivate_and_reactivate_location(): void
    {
        $company = $this->createCompany('PT A');
        $owner = $this->createUser($company, UserRole::OWNER, 'owner@a.test');
        $location = $this->createLocation($company);

        $this->actingAs($owner)->patch(route('locations.status', $location), ['is_active' => false])->assertRedirect();
        $this->assertDatabaseHas('locations', ['id' => $location->id, 'is_active' => false]);
        $this->assertDatabaseHas('audit_logs', ['entity_id' => $location->id, 'action' => 'nonaktifkan']);

        $this->actingAs($owner)->patch(route('locations.status', $location), ['is_active' => true])->assertRedirect();
        $this->assertDatabaseHas('locations', ['id' => $location->id, 'is_active' => true]);
        $this->assertDatabaseHas('audit_logs', ['entity_id' => $location->id, 'action' => 'aktifkan']);
    }

    public function test_index_lists_only_own_company_locations(): void
    {
        // $this->withoutExceptionHandling();

        $companyA = $this->createCompany('PT A');
        $companyB = $this->createCompany('PT B');
        $ownerA = $this->createUser($companyA, UserRole::OWNER, 'owner@a.test');
        $this->createLocation($companyA, 'Kantor A');
        $this->createLocation($companyB, 'Kantor B');

        $this->actingAs($ownerA)->get(route('locations.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('locations/index')
                ->has('locations.data', 1)
                ->where('locations.data.0.name', 'Kantor A')
                ->where('defaults.radius_m', 50));
    }
}
