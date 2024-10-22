<?php

declare(strict_types=1);

use App\Models\Beneficiary;
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
        Schema::create('beneficiary_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class)->constrained()->cascadeOnDelete();
            $table->string('has_family_doctor')->nullable();
            $table->string('family_doctor_name')->nullable();
            $table->string('family_doctor_contact')->nullable();

            $table->string('psychiatric_history')->nullable();
            $table->string('psychiatric_history_notes')->nullable();

            $table->string('criminal_history')->nullable();
            $table->string('criminal_history_notes')->nullable();

            $table->string('studies')->nullable();
            $table->string('occupation')->nullable();
            $table->string('workplace')->nullable();
            $table->string('income')->nullable();

            $table->tinyInteger('elder_care_count')->unsigned()->nullable();

            $table->string('homeownership')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_details');
    }
};
