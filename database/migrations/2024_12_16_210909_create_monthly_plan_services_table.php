<?php

declare(strict_types=1);

use App\Models\MonthlyPlan;
use App\Models\Service;
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
        Schema::create('monthly_plan_services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MonthlyPlan::class);
            $table->foreignIdFor(Service::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('institution')->nullable();
            $table->string('responsible_person')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('objective')->nullable();
            $table->text('service_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_plan_services');
    }
};
