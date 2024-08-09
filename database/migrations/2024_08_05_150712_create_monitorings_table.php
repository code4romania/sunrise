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
        Schema::create('monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class)->constrained();
            $table->date('date')->nullable();
            $table->string('number')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('admittance_date')->nullable();
            $table->string('admittance_disposition')->nullable();
            $table->text('services_in_center')->nullable();
            $table->json('protection_measures')->nullable();
            $table->json('health_measures')->nullable();
            $table->json('legal_measures')->nullable();
            $table->json('psychological_measures')->nullable();
            $table->json('aggressor_relationship')->nullable();
            $table->json('others')->nullable();
            $table->text('progress')->nullable();
            $table->text('observation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitorings');
    }
};
