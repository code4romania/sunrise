<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserStatus;
use App\Models\Organization;
use App\Models\OrganizationUserPermissions;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'status' => fake()->randomElement(UserStatus::values()),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an administrator.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
        ]);
    }

    public function withOrganization(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->organizations()->attach(
                Organization::factory()
                    ->create()
            );
        });
    }

    public function withRolesAndPermissions(int $organizationID)
    {
        return $this->afterCreating(function (User $user) use ($organizationID) {
            $roles = Role::query()
                ->active()
                ->inRandomOrder()
                ->limit(rand(1, 5))
                ->get();

            $casePermissions = [];
            $adminPermissions = [];

            foreach ($roles as $role) {
                $user->roles()->attach($role->id, ['organization_id' => $organizationID]);
                $casePermissions = array_merge(
                    $casePermissions,
                    $role->case_permissions
                        ->map(fn ($permission) => $permission->value)
                        ->toArray()
                );
                $adminPermissions = array_merge(
                    $adminPermissions,
                    $role->ngo_admin_permissions
                        ->map(fn ($permission) => $permission->value)
                        ->toArray()
                );
            }

            OrganizationUserPermissions::factory()
                ->for($user)
                ->create([
                    'organization_id' => $organizationID,
                    'case_permissions' => $casePermissions,
                    'admin_permissions' => $adminPermissions,
                ]);
        });
    }
}
