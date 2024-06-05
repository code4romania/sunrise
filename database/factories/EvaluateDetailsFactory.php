<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EvaluateDetails>
 */
class EvaluateDetailsFactory extends Factory
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
            ->inRandomOrder()
            ->first();

        return [
            'beneficiary_id' => $beneficiary->id,
            'specialist_id' => User::query()
                ->whereHas(
                    'organizations',
                    fn (Builder $query) => $query->where('organizations.id', $beneficiary->organization->id)
                )
                ->inRandomOrder()
                ->first()
                ->id,
            'registered_date' => fake()->date(),
            'file_number' => fake()->randomNumber(),
            'method_of_identifying_the_service' => fake()->text(),
        ];
    }
}
