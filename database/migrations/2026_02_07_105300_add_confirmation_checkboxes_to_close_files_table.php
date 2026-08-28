<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('close_files', function (Blueprint $table) {
            $table->boolean('confirm_closure_criteria')->default(false)->after('close_situation');
            $table->boolean('confirm_documentation')->default(false)->after('confirm_closure_criteria');
        });
    }

    public function down(): void
    {
        Schema::table('close_files', function (Blueprint $table) {
            $table->dropColumn(['confirm_closure_criteria', 'confirm_documentation']);
        });
    }
};
