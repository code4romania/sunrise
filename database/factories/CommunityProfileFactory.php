<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CommunityProfile;
use App\Models\County;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommunityProfile>
 */
class CommunityProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => collect(fake()->paragraphs(fake()->numberBetween(1, 6)))
                ->map(fn (string $paragraph) => "<p>{$paragraph}</p>")
                ->join(''),

            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (CommunityProfile $communityProfile) {
            $communityProfile->counties()->attach(
                County::query()
                    ->inRandomOrder()
                    ->take(fake()->numberBetween(1, 3))
                    ->get()
            );
        });
    }
}
