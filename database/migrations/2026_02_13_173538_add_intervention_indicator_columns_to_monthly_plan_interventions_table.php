<?php

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
        Schema::table('monthly_plan_interventions', function (Blueprint $table) {
            $table->text('expected_results')->nullable()->after('objections');
            $table->text('procedure')->nullable()->after('expected_results');
            $table->text('indicators')->nullable()->after('procedure');
            $table->string('achievement_degree')->nullable()->after('indicators');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_plan_interventions', function (Blueprint $table) {
            $table->dropColumn(['expected_results', 'procedure', 'indicators', 'achievement_degree']);
        });
    }
};
