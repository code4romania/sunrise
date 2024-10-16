<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Country;
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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Country::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(County::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(City::class)->nullable()->constrained()->cascadeOnDelete();
            $table->string('address')->nullable();
            $table->string('environment')->nullable();
            $table->string('address_type')->nullable();
            $table->unsignedBigInteger('addressable_id');
            $table->string('addressable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
