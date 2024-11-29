<?php

declare(strict_types=1);

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
        Schema::table('beneficiary_details', function (Blueprint $table) {
            $table->string('family_doctor_address')->nullable();
            $table->string('investigations_for_psychiatric_pathology')->nullable();
            $table->string('investigations_observations')->nullable();
            $table->string('treatment_for_psychiatric_pathology')->nullable();
            $table->string('treatment_observations')->nullable();
            $table->string('current_contraception')->nullable();
            $table->string('observations_contraception')->nullable();
            $table->string('net_income')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_details', function (Blueprint $table) {
            //
        });
    }
};
