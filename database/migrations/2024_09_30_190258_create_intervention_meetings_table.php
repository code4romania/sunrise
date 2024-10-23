<?php

declare(strict_types=1);

use App\Models\BeneficiaryIntervention;
use App\Models\User;
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
        Schema::create('intervention_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(BeneficiaryIntervention::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('status')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->integer('duration')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intervention_meetings');
    }
};
