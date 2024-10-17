<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Monitoring;
use App\Models\MonitoringChild;
use App\Models\Specialist;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Monitoring>
 */
class MonitoringFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'number' => $this->faker->randomNumber(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'admittance_date' => $this->faker->date(),
            'admittance_disposition' => $this->faker->text(100),
            'services_in_center' => $this->faker->text(),
            'protection_measures' => [
                'objection' => $this->faker->text(),
                'activity' => $this->faker->text(),
                'conclusion' => $this->faker->text(),
            ],
            'health_measures' => [
                'objection' => $this->faker->text(),
                'activity' => $this->faker->text(),
                'conclusion' => $this->faker->text(),
            ],
            'legal_measures' => [
                'objection' => $this->faker->text(),
                'activity' => $this->faker->text(),
                'conclusion' => $this->faker->text(),
            ],
            'psychological_measures' => [
                'objection' => $this->faker->text(),
                'activity' => $this->faker->text(),
                'conclusion' => $this->faker->text(),
            ],
            'aggressor_relationship' => [
                'objection' => $this->faker->text(),
                'activity' => $this->faker->text(),
                'conclusion' => $this->faker->text(),
            ],
            'others' => [
                'objection' => $this->faker->text(),
                'activity' => $this->faker->text(),
                'conclusion' => $this->faker->text(),
            ],
            'progress' => $this->faker->text(),
            'observation' => $this->faker->text(),
        ];
    }

    public function configure(): static
    {
        return $this
            ->afterCreating(function (Monitoring $monitoring) {
                $children = $monitoring->beneficiary->children;

                foreach ($children as $child) {
                    MonitoringChild::factory()
                        ->for($monitoring)
                        ->create(['name' => $child['name'] ?? null,
                            'status' => $child['status'] ?? null,
                            'age' => $child['age'] ?? null,
                            'birthdate' => $child['birthdate'] ?? null,
                        ]);
                }

                $specialists = $monitoring->beneficiary->specialistsTeam;
                $specialists = collect(fake()->randomElements($specialists, rand(1, $specialists->count())));
                $specialists = $specialists->map(fn (Specialist $specialist) => ['user_id' => $specialist->user_id, 'role_id' => $specialist->role_id])
                    ->toArray();

                Specialist::factory()
                    ->for($monitoring, 'specialistable')
                    ->state(new Sequence(...$specialists))
                    ->count(\count($specialists))
                    ->create();
            });
    }
}
