<?php

declare(strict_types=1);

use App\Enums\CaseStatus;
use App\Models\Beneficiary;
use App\Models\Country;
use App\Models\Ethnicity;
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
            $table->ulid();
            $table->foreignIdFor(Beneficiary::class, 'initial_id')
                ->nullable()
                ->constrained('beneficiaries')
                ->nullOnDelete();

            $table->timestamps();

            $table->foreignIdFor(Organization::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status')->default(CaseStatus::ACTIVE);

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('prior_name')->nullable();
            $table->string('full_name')->virtualAs(<<<'SQL'
                NULLIF(CONCAT_WS(" ", first_name, last_name, NULLIF(CONCAT("(", prior_name, ")"), "()")), " ")
            SQL);

            $table->string('civil_status')->nullable();
            $table->decimal('cnp', 13, 0)->nullable();
            $table->string('gender')->nullable();
            $table->string('birthplace')->nullable();
            $table->date('birthdate')->nullable();

            $table->string('id_type')->nullable();
            $table->string('id_serial')->nullable();
            $table->string('id_number')->nullable();

            $table->foreignIdFor(Country::class, 'citizenship_id')
                ->nullable()
                ->constrained('countries')
                ->cascadeOnDelete();

            $table->foreignIdFor(Ethnicity::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->county('legal_residence');
            $table->city('legal_residence');
            $table->string('legal_residence_address')->nullable();
            $table->string('legal_residence_environment')->nullable();

            $table->boolean('same_as_legal_residence')->default(false);
            $table->county('effective_residence');
            $table->city('effective_residence');
            $table->string('effective_residence_address')->nullable();
            $table->string('effective_residence_environment')->nullable();

            $table->string('primary_phone')->nullable();
            $table->string('backup_phone')->nullable();
            $table->text('contact_notes')->nullable();

            $table->boolean('doesnt_have_children');
            $table->tinyInteger('children_total_count')->unsigned()->nullable();
            $table->tinyInteger('children_care_count')->unsigned()->nullable();
            $table->tinyInteger('children_under_10_care_count')->unsigned()->nullable();
            $table->tinyInteger('children_10_18_care_count')->unsigned()->nullable();
            $table->tinyInteger('children_18_care_count')->unsigned()->nullable();
            $table->tinyInteger('children_accompanying_count')->unsigned()->nullable();
            $table->json('children')->nullable();

            $table->text('children_notes')->nullable();

            $table->string('has_family_doctor')->nullable();
            $table->string('family_doctor_name')->nullable();
            $table->string('family_doctor_contact')->nullable();

            $table->string('psychiatric_history')->nullable();
            $table->string('psychiatric_history_notes')->nullable();

            $table->string('criminal_history')->nullable();
            $table->string('criminal_history_notes')->nullable();

            $table->string('studies')->nullable();
            $table->string('occupation')->nullable();
            $table->string('workplace')->nullable();
            $table->string('income')->nullable();

            $table->tinyInteger('elder_care_count')->unsigned()->nullable();

            $table->string('homeownership')->nullable();
        });
    }
};
