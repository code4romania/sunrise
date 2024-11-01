<?php

declare(strict_types=1);

use App\Models\InterventionService;
use App\Models\OrganizationServiceIntervention;
use App\Models\User;
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
        Schema::create('beneficiary_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InterventionService::class);
            $table->foreignIdFor(OrganizationServiceIntervention::class);
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('start_date_interval')->nullable();
            $table->date('end_date_interval')->nullable();
            $table->text('objections')->nullable();
            $table->text('expected_results')->nullable();
            $table->text('procedure')->nullable();
            $table->text('indicators')->nullable();
            $table->text('achievement_degree')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_interventions');
    }
};
