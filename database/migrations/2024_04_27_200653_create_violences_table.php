<?php

declare(strict_types=1);

use App\Models\Beneficiary;
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
        Schema::create('violences', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class);
            $table->json('violence_types');
            $table->string('violence_primary_type');
            $table->string('frequency_violence');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violences');
    }
};
