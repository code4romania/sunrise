<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role = fake()->unique()->randomElement(Role::options());
        $casePermissions = fake()->randomElements(CasePermission::values(), rand(1, 3));
        $status = fake()->boolean();
        $caseManager = false;
        if ($role === Role::MANAGER->getLabel()) {
            $status = true;
            $caseManager = true;
        }

        return [
            'name' => $role,
            'status' => $status,
            'case_permissions' => $casePermissions,
            'ngo_admin_permissions' => fake()->randomElements(AdminPermission::values(), rand(1, 3)),
            'case_manager' => $caseManager,
        ];
    }
}
