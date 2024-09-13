<?php

declare(strict_types=1);

use App\Models\Beneficiary;
use App\Models\CaseTeam;
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
        Schema::create('close_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(CaseTeam::class)->nullable()->constrained()->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->string('number')->nullable();
            $table->date('admittance_date')->nullable();
            $table->date('exit_date')->nullable();
            $table->string('admittance_reason')->nullable();
            $table->string('admittance_details')->nullable();
            $table->string('close_method')->nullable();
            $table->string('institution_name')->nullable();
            $table->string('beneficiary_request')->nullable();
            $table->string('other_details')->nullable();
            $table->string('close_situation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_files');
    }
};
