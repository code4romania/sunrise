<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('beneficiary_situations', function ($table) {
            $table->string('moment_of_evaluation')->nullable()->change();
            $table->text('description_of_situation')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiary_situations', function ($table) {
            $table->string('moment_of_evaluation')->change();
            $table->text('description_of_situation')->change();
        });
    }
};
