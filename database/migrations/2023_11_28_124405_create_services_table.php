<?php

declare(strict_types=1);

use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('counseling_sheet')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('model_has_services', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->foreignIdFor(Service::class)->constrained()->cascadeOnDelete();
            $table->boolean('is_visible')->default(false);
            $table->boolean('is_available')->default(false);
        });
    }
};
