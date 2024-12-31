<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AdmittanceReason;
use App\Enums\CloseMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CloseFile>
 */
class CloseFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $closeMethod = $this->faker->randomElement(CloseMethod::values());

        return [
            'date' => $this->faker->date(),
            'admittance_date' => $this->faker->date(),
            'exit_date' => $this->faker->date(),
            'admittance_reason' => $this->faker->randomElements(AdmittanceReason::values(), rand(0, 5)),
            'admittance_details' => $this->faker->text(100),
            'close_method' => $closeMethod,
            'institution_name' => CloseMethod::isValue($closeMethod, CloseMethod::TRANSFER_TO) ? $this->faker->text(100) : null,
            'beneficiary_request' => CloseMethod::isValue($closeMethod, CloseMethod::BENEFICIARY_REQUEST) ? $this->faker->text(100) : null,
            'other_details' => CloseMethod::isValue($closeMethod, CloseMethod::OTHER) ? $this->faker->text(100) : null,
            'close_situation' => $this->faker->text,
        ];
    }
}
