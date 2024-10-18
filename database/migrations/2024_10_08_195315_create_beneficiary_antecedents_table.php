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
        Schema::create('beneficiary_antecedents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class)->constrained()->cascadeOnDelete();
            $table->string('has_police_reports')->nullable();
            $table->smallInteger('police_report_count')->unsigned()->nullable();
            $table->string('has_medical_reports')->nullable();
            $table->smallInteger('medical_report_count')->unsigned()->nullable();
            $table->string('has_protection_order')->nullable();
            $table->string('electronically_monitored')->nullable();
            $table->string('protection_order_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_antecedents');
    }
};
