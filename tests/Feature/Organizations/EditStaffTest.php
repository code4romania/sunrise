<?php

declare(strict_types=1);

use App\Filament\Organizations\Resources\Staff\Pages\EditStaff;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class, RefreshDatabase::class);

it('keeps existing staff role when editing without role changes', function () {
    $editor = User::factory()->withOrganization()->create([
        'ngo_admin' => true,
    ]);
    $organization = $editor->organizations->firstOrFail();
    $editor->update(['institution_id' => $organization->institution_id]);

    $this->actingAs($editor);
    Filament::setTenant($organization);
    Filament::bootCurrentPanel();

    $role = Role::factory()->create(['status' => true]);

    $staff = User::factory()->create([
        'phone_number' => '0712345678',
    ]);
    $staff->organizations()->attach($organization);
    $staff->roles()->attach($role->id, ['organization_id' => $organization->id]);

    livewire(EditStaff::class, [
        'record' => $staff->getKey(),
        'tenant' => $organization,
    ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(
        $staff->fresh()->rolesInOrganization()->pluck('roles.id')->all()
    )->toContain($role->id);
});
