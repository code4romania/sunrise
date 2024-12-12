<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BeneficiaryIntervention;
use App\Models\InterventionService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InterventionService>
 */
class InterventionServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institution' => $this->faker->company(),
            'objections' => $this->faker->text(),
        ];
    }

    public function withBeneficiaryIntervention(): self
    {
        return $this->afterCreating(function (InterventionService $interventionService) {
//            dd($interventionService->organizationService->interventions);
//            $interventionService->load('organizationService', 'beneficiary');
            BeneficiaryIntervention::factory()
                ->for($interventionService)
                ->state(
                    fn () => [
                        'organization_service_intervention_id' => $interventionService->organizationServiceWithoutStatusCondition
                            ->interventions
                            ->random()
                            ->id,
                        'specialist_id' => $interventionService->beneficiary
                            ->specialistsTeam
                            ->random()
                            ->id,
                    ]
                )
                ->count(rand(1, 5))
                ->create();
        });
    }
}
