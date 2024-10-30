<?php

declare(strict_types=1);

use App\Enums\CaseStatus;
use App\Models\Beneficiary;
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

            $table->string('citizenship')->nullable();
            $table->string('ethnicity')->nullable();

            $table->boolean('same_as_legal_residence')->default(false);

            $table->string('primary_phone')->nullable();
            $table->string('backup_phone')->nullable();
            $table->string('email')->nullable();
            $table->text('contact_notes')->nullable();

            $table->boolean('doesnt_have_children');
            $table->tinyInteger('children_total_count')->unsigned()->nullable();
            $table->tinyInteger('children_care_count')->unsigned()->nullable();
            $table->tinyInteger('children_under_10_care_count')->unsigned()->nullable();
            $table->tinyInteger('children_10_18_care_count')->unsigned()->nullable();
            $table->tinyInteger('children_18_care_count')->unsigned()->nullable();
            $table->tinyInteger('children_accompanying_count')->unsigned()->nullable();

            $table->text('children_notes')->nullable();
        });
    }
};
