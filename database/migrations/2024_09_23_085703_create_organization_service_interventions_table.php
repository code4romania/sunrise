<?php

declare(strict_types=1);

use App\Models\Organization;
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
        Schema::create('organization_service_interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('service_intervention_id');
            $table->foreign('service_intervention_id', 'service_intervention_id')->references('id')->on('service_interventions')->cascadeOnDelete();
            $table->unsignedBigInteger('organization_service_id');
            $table->foreign('organization_service_id', 'organization_service_id')->references('id')->on('organization_services')->cascadeOnDelete();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_service_interventions');
    }
};
