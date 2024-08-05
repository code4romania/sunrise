<?php

declare(strict_types=1);

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
        Schema::create('monitoring_children', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Monitoring::class)->constrained();
            $table->string('name')->nullable();
            $table->string('state')->nullable();
            $table->string('age')->nullable();
            // TODO change in birthdate
            $table->date('birth_date')->nullable();
            $table->string('aggressor_relationship')->nullable();
            $table->string('maintenance_sources')->nullable();
            $table->string('location')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_children');
    }
};
