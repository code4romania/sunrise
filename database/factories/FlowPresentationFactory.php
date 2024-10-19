<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ActLocation;
use App\Enums\NotificationMode;
use App\Enums\Notifier;
use App\Enums\PresentationMode;
use App\Enums\ReferralMode;
use App\Models\FlowPresentation;
use App\Models\ReferringInstitution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlowPresentation>
 */
class FlowPresentationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'presentation_mode' => fake()->randomElement(PresentationMode::values()),
            'referral_mode' => fake()->randomElement(ReferralMode::values()),
            'notifier' => fake()->randomElement(Notifier::values()),
            'notification_mode' => fake()->randomElement(NotificationMode::values()),

            'act_location' => fake()->randomElement(ActLocation::values()),
        ];
    }

    public function configure()
    {
        $referringInstitutions = ReferringInstitution::all();

        return $this
            ->afterMaking(function (FlowPresentation $flowPresentation) use ($referringInstitutions) {
                if (PresentationMode::isValue($flowPresentation->presentation_method, PresentationMode::FORWARDED)) {
                    $flowPresentation->referringInstitution()->attach(
                        $referringInstitutions->random()
                    );
                }

                $flowPresentation->firstCalledInstitution()->associate(
                    $referringInstitutions->random()
                );
            })
            ->afterCreating(function (FlowPresentation $flowPresentation) use ($referringInstitutions) {
                $flowPresentation->otherCalledInstitution()->sync(
                    $referringInstitutions->random(fake()->numberBetween(1, 4)),
                );
            });
    }
}
