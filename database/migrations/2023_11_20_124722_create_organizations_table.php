<?php

declare(strict_types=1);

use App\Models\Institution;
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
            $table->foreignIdFor(Institution::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('short_name')->nullable();
            $table->string('main_activity')->nullable();

            $table->timestamps();
        });

        Schema::create('model_has_organizations', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
        });
    }
};
