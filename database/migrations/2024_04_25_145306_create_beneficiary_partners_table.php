<?php

declare(strict_types=1);

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
        Schema::create('beneficiary_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\Beneficiary::class);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('age')->nullable();
            $table->string('occupation')->nullable();
            $table->county('legal_residence');
            $table->city('legal_residence');
            $table->string('legal_residence_address')->nullable();
            $table->boolean('same_as_legal_residence')->default(false);
            $table->county('effective_residence');
            $table->city('effective_residence');
            $table->string('effective_residence_address')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiary_partners');
    }
};
