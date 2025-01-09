<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiary_details', function (Blueprint $table) {
            $table->string('drug_consumption')->nullable();
            $table->json('drug_types')->nullable();
            $table->string('other_current_medication')->nullable();
            $table->string('medication_observations')->nullable();
        });
    }
};
