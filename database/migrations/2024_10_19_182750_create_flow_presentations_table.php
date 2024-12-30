<?php

declare(strict_types=1);

use App\Models\Beneficiary;
use App\Models\ReferringInstitution;
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
        Schema::create('flow_presentations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Beneficiary::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('presentation_mode')->nullable();
            $table->foreignIdFor(ReferringInstitution::class)
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->json('referral_mode')->nullable();
            $table->string('notifier')->nullable();
            $table->string('notification_mode')->nullable();
            $table->string('notifier_other')->nullable();
            $table->json('act_location')->nullable();
            $table->string('act_location_other')->nullable();
            $table->foreignIdFor(ReferringInstitution::class, 'first_called_institution_id')
                ->nullable()
                ->constrained('referring_institutions')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flow_presentations');
    }
};
