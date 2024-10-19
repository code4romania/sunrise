<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RecommendationService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DetailedEvaluationResult>
 */
class DetailedEvaluationResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $recommendationServices = [];
        foreach (RecommendationService::values() as $service) {
            if (fake()->boolean) {
                $recommendationServices[] = $service;
            }
        }

        return [
            'recommendation_services' => $recommendationServices,
            'other_services_description' => fake()->text(100),
            'recommendations_for_intervention_plan' => fake()->text(),
        ];
    }
}
