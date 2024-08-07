<?php

declare(strict_types=1);

use App\Models\CaseTeam;
use App\Models\Monitoring;
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
        Schema::create('monitoring_specialists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Monitoring::class);
            $table->foreignIdFor(CaseTeam::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_specialists');
    }
};
