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
            $table->string('health_insurance')->nullable();
            $table->json('health_status')->nullable();
            $table->text('observations_chronic_diseases')->nullable();
            $table->text('observations_degenerative_diseases')->nullable();
            $table->text('observations_mental_illness')->nullable();

            $table->string('disabilities')->nullable();
            $table->json('type_of_disability')->nullable();
            $table->string('degree_of_disability')->nullable();
            $table->text('observations_disability')->nullable();

            $table->json('income_source')->nullable();
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
