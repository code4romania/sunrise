<?php

declare(strict_types=1);

use App\Models\Beneficiary;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('risk_factors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class);
            $table->string('previous_acts_of_violence');
            $table->string('previous_acts_of_violence_description')->nullable();
            $table->string('violence_against_children_or_family_members');
            $table->string('violence_against_children_or_family_members_description')->nullable();
            $table->string('abuser_exhibited_generalized_violent');
            $table->string('abuser_exhibited_generalized_violent_description')->nullable();
            $table->string('protection_order_in_past');
            $table->string('protection_order_in_past_description')->nullable();
            $table->string('abuser_violated_protection_order');
            $table->string('abuser_violated_protection_order_description')->nullable();
            $table->string('frequency_of_violence_acts');
            $table->string('frequency_of_violence_acts_description')->nullable();
            $table->string('use_weapons_in_act_of_violence');
            $table->string('use_weapons_in_act_of_violence_description')->nullable();
            $table->string('controlling_and_isolating');
            $table->string('controlling_and_isolating_description')->nullable();
            $table->string('stalked_or_harassed');
            $table->string('stalked_or_harassed_description')->nullable();
            $table->string('sexual_violence');
            $table->string('sexual_violence_description')->nullable();
            $table->string('death_threats');
            $table->string('death_threats_description')->nullable();
            $table->string('strangulation_attempt');
            $table->string('strangulation_attempt_description')->nullable();
            $table->string('FR_S3Q1');
            $table->string('FR_S3Q1_description')->nullable();
            $table->string('FR_S3Q2');
            $table->string('FR_S3Q2_description')->nullable();
            $table->string('FR_S3Q3');
            $table->string('FR_S3Q3_description')->nullable();
            $table->string('FR_S3Q4');
            $table->string('FR_S3Q4_description')->nullable();
            $table->string('FR_S4Q1');
            $table->string('FR_S4Q1_description')->nullable();
            $table->string('FR_S4Q2');
            $table->string('FR_S4Q2_description')->nullable();
            $table->string('FR_S5Q1');
            $table->string('FR_S5Q1_description')->nullable();
            $table->string('FR_S5Q2');
            $table->string('FR_S5Q2_description')->nullable();
            $table->string('FR_S5Q3');
            $table->string('FR_S5Q3_description')->nullable();
            $table->string('FR_S5Q4');
            $table->string('FR_S5Q4_description')->nullable();
            $table->string('FR_S5Q5');
            $table->string('FR_S5Q5_description')->nullable();
            $table->string('FR_S6Q1');
            $table->string('FR_S6Q1_description')->nullable();
            $table->string('FR_S6Q2');
            $table->string('FR_S6Q2_description')->nullable();
            $table->string('risk_level')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_factors');
    }
};
