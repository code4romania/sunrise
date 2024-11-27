<?php

declare(strict_types=1);

use App\Models\InterventionPlan;
use App\Models\Result;
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
        Schema::create('intervention_plan_results', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InterventionPlan::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Result::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->date('retried_at')->nullable();
            $table->boolean('retried')->default(false);
            $table->boolean('lost_from_monitoring')->default(false);
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_plan_results');
    }
};
