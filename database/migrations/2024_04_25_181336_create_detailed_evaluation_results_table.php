<?php

declare(strict_types=1);

use App\Enums\RecommendationService;
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
        Schema::create('detailed_evaluation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class);
            foreach (RecommendationService::values() as $value) {
                $table->boolean($value)->default(false);
            }

            $table->text('other_services_description')->nullable();
            $table->text('recommendations_for_intervention_plan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailed_evaluation_results');
    }
};
