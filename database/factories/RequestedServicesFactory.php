<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RecommendationService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestedServices>
 */
class RequestedServicesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $requestedServices = [];
        foreach (RecommendationService::values() as $service) {
            if (fake()->boolean) {
                $requestedServices[] = $service;
            }
        }

        return [
            'requested_services' => $requestedServices,
            'other_services_description' => fake()->text(100),
        ];
    }
}
