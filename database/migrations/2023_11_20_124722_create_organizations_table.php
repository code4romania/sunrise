<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\County;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->timestamps();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('short_name')->nullable();
            $table->string('type')->nullable();
            $table->string('cif')->nullable();
            $table->string('main_activity')->nullable();

            $table->foreignIdFor(County::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(City::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('address')->nullable();

            $table->string('reprezentative_name')->nullable();
            $table->string('reprezentative_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
        });

        Schema::create('model_has_organizations', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
        });
    }
};
