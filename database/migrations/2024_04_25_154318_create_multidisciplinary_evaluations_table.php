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
        Schema::create('multidisciplinary_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class);
            $table->string('applicant');
            $table->string('reporting_by')->nullable();
            $table->text('medical_need')->nullable();
            $table->text('professional_need')->nullable();
            $table->text('emotional_and_psychological_need')->nullable();
            $table->text('social_economic_need')->nullable();
            $table->text('legal_needs')->nullable();
            $table->text('extended_family')->nullable();
            $table->text('family_social_integration')->nullable();
            $table->text('income')->nullable();
            $table->text('community_resources')->nullable();
            $table->text('house')->nullable();
            $table->text('risk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('multidisciplinary_evaluations');
    }
};
