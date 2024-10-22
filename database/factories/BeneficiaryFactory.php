<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ActLocation;
use App\Enums\AddressType;
use App\Enums\CaseStatus;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Enums\IDType;
use App\Models\Address;
use App\Models\Aggressor;
use App\Models\Beneficiary;
use App\Models\BeneficiaryAntecedents;
use App\Models\BeneficiaryDetails;
use App\Models\BeneficiaryPartner;
use App\Models\BeneficiarySituation;
use App\Models\CaseTeam;
use App\Models\Children;
use App\Models\CloseFile;
use App\Models\DetailedEvaluationResult;
use App\Models\Document;
use App\Models\EvaluateDetails;
use App\Models\FlowPresentation;
use App\Models\Meeting;
use App\Models\Monitoring;
use App\Models\MultidisciplinaryEvaluation;
use App\Models\RequestedServices;
use App\Models\RiskFactors;
use App\Models\Specialist;
use App\Models\User;
use App\Models\Violence;
use App\Models\ViolenceHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Beneficiary>
 */
class BeneficiaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $birthdate = fake()
            ->dateTimeBetween('1900-01-01', 'now')
            ->format('Y-m-d');

        $gender = fake()->randomElement(Gender::values());

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'prior_name' => fake()->boolean(25) ? fake()->lastName() : null,

            'civil_status' => fake()->randomElement(CivilStatus::values()),

            'gender' => $gender,
            'birthplace' => fake()->sentence(),
            'birthdate' => $birthdate,

            'primary_phone' => fake()->phoneNumber(),
            'backup_phone' => fake()->boolean(25) ? fake()->phoneNumber() : null,

            'status' => fake()->randomElement(CaseStatus::values()),
            'doesnt_have_children' => true,

        ];
    }

    public function withCNP(): static
    {
        return $this->state(fn (array $attributes) => [
            'cnp' => rescue(
                fn () => fake()->cnp(gender: $attributes['gender'], dateOfBirth: $attributes['birthdate']),
                fn () => fake()->cnp(dateOfBirth: $attributes['birthdate']),
                false
            ),
        ]);
    }

    public function withID(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_type' => fake()->randomElement(IDType::values()),
            'id_serial' => fake()->lexify('??'),
            'id_number' => fake()->numerify('######'),
        ]);
    }

    public function withLegalResidence(): static
    {
        return $this->afterCreating(function (Beneficiary $beneficiary) {
            $beneficiary->same_as_legal_residence = true;
            $beneficiary->save();

            Address::factory()
                ->for($beneficiary, 'addressable')
                ->state(['address_type' => AddressType::LEGAL_RESIDENCE])
                ->create();
        });
    }

    public function withEffectiveResidence(): static
    {
        return $this->afterCreating(function (Beneficiary $beneficiary) {
            $beneficiary->same_as_legal_residence = true;
            $beneficiary->save();

            Address::factory()
                ->for($beneficiary, 'addressable')
                ->state(['address_type' => AddressType::EFFECTIVE_RESIDENCE])
                ->create();
        });
    }

    public function withContactNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'contact_notes' => fake()->paragraphs(asText: true),
        ]);
    }

    public function withChildren(): static
    {
        return $this->state(fn (array $attributes) => [
            'doesnt_have_children' => false,
            'children_total_count' => fake()->numberBetween(1, 10),
            'children_care_count' => fake()->numberBetween(1, 10),
            'children_under_10_care_count' => fake()->numberBetween(1, 10),
            'children_10_18_care_count' => fake()->numberBetween(1, 10),
            'children_18_care_count' => fake()->numberBetween(1, 10),
            'children_accompanying_count' => fake()->numberBetween(1, 10),
        ]);
    }

    public function withAntecedents(): static
    {
        return $this
            ->afterCreating(function (Beneficiary $beneficiary) {
                BeneficiaryAntecedents::factory()
                    ->for($beneficiary)
                    ->create();
            });
    }

    public function withFlowPresentation(): static
    {
        return $this->afterCreating(function (Beneficiary $beneficiary) {
            FlowPresentation::factory()
                ->for($beneficiary)
                ->create();
        });
    }

    public function withBeneficiaryDetails(): static
    {
        return $this->afterCreating(function (Beneficiary $beneficiary) {
            BeneficiaryDetails::factory()
                ->for($beneficiary)
                ->create();
        });
    }

    public function configure(): static
    {
        return $this
            ->afterCreating(function (Beneficiary $beneficiary) {
                Children::factory()
                    ->for($beneficiary)
                    ->count(rand(1, 5))
                    ->create();

                BeneficiaryPartner::factory()
                    ->for($beneficiary)
                    ->create();

                BeneficiarySituation::factory()
                    ->for($beneficiary)
                    ->create();

                $count = rand(1, 5);
                $users = User::query()
                    ->whereHas(
                        'organizations',
                        fn (Builder $query) => $query->where('organizations.id', $beneficiary->organization->id)
                    )
                    ->with('roles')
                    ->inRandomOrder()
                    ->limit($count)
                    ->get()
                    ->map(function ($item) {
                        $roles = fake()->randomElements($item->roles, rand(1, \count($item->roles)));
                        $state = [];
                        foreach ($roles as $role) {
                            $state[] = [
                                'user_id' => $item->id,
                                'role_id' => $role->id,
                            ];
                        }

                        return $state;
                    })
                    ->toArray();

                $users = array_merge(...$users);

                Specialist::factory()
                    ->for($beneficiary, 'specialistable')
                    ->state(new Sequence(...$users))
                    ->count(\count($users))
                    ->create();

                DetailedEvaluationResult::factory()
                    ->for($beneficiary)
                    ->create();

                EvaluateDetails::factory()
                    ->for($beneficiary)
                    ->state(['specialist_id' => User::query()
                        ->whereHas(
                            'organizations',
                            fn (Builder $query) => $query->where('organizations.id', $beneficiary->organization->id)
                        )
                        ->inRandomOrder()
                        ->first()
                        ->id,
                    ])
                    ->create();

                Meeting::factory()
                    ->for($beneficiary)
                    ->count(rand(1, 5))
                    ->create();

                MultidisciplinaryEvaluation::factory()
                    ->for($beneficiary)
                    ->create();

                RiskFactors::factory()
                    ->for($beneficiary)
                    ->create();

                Violence::factory()
                    ->for($beneficiary)
                    ->create();

                ViolenceHistory::factory()
                    ->for($beneficiary)
                    ->count(rand(1, 5))
                    ->create();

                RequestedServices::factory()
                    ->for($beneficiary)
                    ->create();

                Document::factory()
                    ->for($beneficiary)
                    ->count(rand(1, 5))
                    ->create();

                Aggressor::factory()
                    ->for($beneficiary)
                    ->create();

                Monitoring::factory()
                    ->for($beneficiary)
                    ->create();

                if (CaseStatus::isValue($beneficiary->status, CaseStatus::CLOSED)) {
                    CloseFile::factory()
                        ->for($beneficiary)
                        ->create([
                            'user_id' => $this->faker->randomElement($beneficiary->specialistsMembers)->id,
                        ]);
                }
            });
    }
}
