<?php

declare(strict_types=1);

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
        Schema::table('aggressors', function (Blueprint $table) {
            $table->string('has_protection_order')->nullable();
            $table->string('electronically_monitored')->nullable();
            $table->string('protection_order_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aggressors', function (Blueprint $table) {
            //
        });
    }
};
