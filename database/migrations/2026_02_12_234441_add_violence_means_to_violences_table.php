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
        Schema::table('violences', function (Blueprint $table) {
            $table->json('violence_means')->nullable()->after('frequency_violence');
            $table->string('violence_means_specify', 100)->nullable()->after('violence_means');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('violences', function (Blueprint $table) {
            $table->dropColumn(['violence_means', 'violence_means_specify']);
        });
    }
};
