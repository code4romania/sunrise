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
        Schema::create('beneficiary_situations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class);
            $table->string('moment_of_evaluation')->nullable();
            $table->text('description_of_situation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_situations');
    }
};
