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
        Schema::create('requested_services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class);
            $table->boolean('psychological_advice')->default(false);
            $table->boolean('legal_advice')->default(false);
            $table->boolean('legal_assistance')->default(false);
            $table->boolean('prenatal_advice')->default(false);
            $table->boolean('social_advice')->default(false);
            $table->boolean('medical_services')->default(false);
            $table->boolean('medical_payment')->default(false);
            $table->boolean('securing_residential_spaces')->default(false);
            $table->boolean('occupational_program_services')->default(false);
            $table->boolean('educational_services_for_children')->default(false);
            $table->boolean('temporary_shelter_services')->default(false);
            $table->boolean('protection_order')->default(false);
            $table->boolean('crisis_assistance')->default(false);
            $table->boolean('safety_plan')->default(false);
            $table->boolean('other_services')->default(false);
            $table->text('other_services_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requested_services');
    }
};
