<?php

declare(strict_types=1);

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
        Schema::create('referring_institutions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->tinyInteger('order')->default(0);
        });

        collect([
            'Poliție',
            'IML',
            'UPU-SMURD',
            'Medic de familie',
            'Primărie',
            'DGASPC',
            'ONG',
            'Alta',
        ])->each(fn (string $name) => ReferringInstitution::forceCreate(['name' => $name]));

        Schema::create('model_has_referring_institutions', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->foreignIdFor(ReferringInstitution::class, 'institution_id')
                ->constrained('referring_institutions')
                ->cascadeOnDelete();
        });
    }
};
