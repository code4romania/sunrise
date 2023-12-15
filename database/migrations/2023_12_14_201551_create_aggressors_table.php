<?php

declare(strict_types=1);

use App\Models\Beneficiary;
use App\Models\Country;
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
        Schema::create('aggressors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignIdFor(Beneficiary::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Country::class, 'citizenship_id')
                ->nullable()
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->string('relationship')->nullable();
            $table->tinyInteger('age')->unsigned()->nullable();
            $table->string('gender')->nullable();
            $table->string('civil_status')->nullable();

            $table->string('studies')->nullable();
            $table->string('occupation')->nullable();

            $table->string('has_violence_history')->nullable();
            $table->json('violence_types')->nullable();

            $table->string('has_psychiatric_history')->nullable();
            $table->string('psychiatric_history_notes')->nullable();

            $table->string('has_drug_history')->nullable();
            $table->json('drugs')->nullable();

            $table->json('legal_history')->nullable();
            $table->string('has_protection_order')->nullable();
            $table->string('protection_order_notes')->nullable();
        });
    }
};
