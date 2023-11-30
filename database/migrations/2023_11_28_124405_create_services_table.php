<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->timestamps();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignIdFor(Organization::class)->constrained()->cascadeOnDelete();
        });

        Schema::create('model_has_service', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->foreignIdFor(Service::class)->constrained()->cascadeOnDelete();
        });
    }
};
