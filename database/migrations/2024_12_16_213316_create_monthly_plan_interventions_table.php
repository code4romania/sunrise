<?php

declare(strict_types=1);

use App\Models\MonthlyPlan;
use App\Models\MonthlyPlanService;
use App\Models\ServiceIntervention;
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
        Schema::create('monthly_plan_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MonthlyPlan::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(MonthlyPlanService::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ServiceIntervention::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('objections')->nullable();
            $table->string('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_plan_interventions');
    }
};
