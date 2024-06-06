<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaseTeam>
 */
class CaseTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return  [
            'roles' => collect(fake()->randomElements(Role::values(), rand(1, 5))),
        ];
    }
}
