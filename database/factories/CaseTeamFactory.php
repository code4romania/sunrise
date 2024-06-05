<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
        $beneficiary = Beneficiary::query()
            ->with('organization')
            ->has('organization')
            ->inRandomOrder()
            ->first();

        return  [
            'beneficiary_id' => $beneficiary->id,
            'user_id' => User::query()
                ->whereHas(
                    'organizations',
                    fn (Builder $query) => $query->where('organizations.id', $beneficiary->organization->id)
                )
                ->inRandomOrder()
                ->first()
                ->id,
            'roles' => collect(fake()->randomElements(Role::values(), rand(1, 5))),
        ];
    }
}
