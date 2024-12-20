<?php

declare(strict_types=1);

use App\Models\InterventionPlan;
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
        Schema::create('monthly_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(InterventionPlan::class);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignIdFor(User::class, 'case_manager_user_id');
            $table->json('specialists');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_plans');
    }
};
