<?php

declare(strict_types=1);

use App\Models\InterventionPlan;
use App\Models\OrganizationService;
use App\Models\Specialist;
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
        Schema::create('intervention_services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InterventionPlan::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(OrganizationService::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Specialist::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('institution')->nullable();
            $table->date('start_date')->nullable();
            $table->date('start_date_interval')->nullable();
            $table->date('end_date_interval')->nullable();
            $table->text('objections')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_services');
    }
};
