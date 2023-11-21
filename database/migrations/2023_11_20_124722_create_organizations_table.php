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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('avatar_url')->nullable();
        });

        Schema::create('model_has_organizations', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
        });
    }
};
