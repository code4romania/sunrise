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
        Schema::create('detailed_evaluation_specialists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class)->constrained()->cascadeOnDelete();
            $table->string('full_name')->nullable();
            $table->string('institution')->nullable();
            $table->string('relationship')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailed_evaluation_specialists');
    }
};
