<?php

declare(strict_types=1);

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('prior_name')->nullable();

            $table->string('civil_status')->nullable();
            $table->decimal('cnp', 13, 0)->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('ethnicity')->nullable();

            $table->string('id_type')->nullable();
            $table->string('id_serial')->nullable();
            $table->string('id_number')->nullable();

            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
        });
    }
};
