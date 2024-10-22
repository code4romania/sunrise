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
        return [
            'name' => fake()->unique()->randomElement(Role::options()),
            'status' => fake()->boolean(),
            'case_permissions' => fake()->randomElements(CasePermission::values(), rand(1, 4)),
            'ngo_admin_permissions' => fake()->randomElements(AdminPermission::values(), rand(1, 3)),
        ];
    }
}
