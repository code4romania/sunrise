<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk_factors', function (Blueprint $table) {
            $table->boolean('extended_family_can_not_provide')->nullable();
            $table->boolean('friends_can_not_provide')->nullable();
        });
    }
};
