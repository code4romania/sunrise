<?php

declare(strict_types=1);

use App\Enums\InstitutionStatus;
use App\Models\City;
use App\Models\County;
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
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();

            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('short_name')->nullable();
            $table->string('type');
            $table->string('cif');
            $table->string('main_activity');
            $table->string('area');

            $table->foreignIdFor(County::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(City::class)->constrained()->cascadeOnDelete();
            $table->string('address');

            $table->string('representative_name');
            $table->string('representative_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('contact_person');
            $table->string('contact_person_email');
            $table->string('contact_person_phone');
            $table->string('website')->nullable();

            $table->string('status')->default(InstitutionStatus::PENDING->value);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
