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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->string('status')->nullable()->after('phone_number');
            $table->json('roles')->nullable()->after('status');
            $table->boolean('can_be_case_manager')->nullable()->after('roles');
            $table->boolean('has_access_to_all_cases')->nullable()->after('can_be_case_manager');
            $table->json('case_permissions')->nullable()->after('has_access_to_all_cases');
            $table->json('admin_permissions')->nullable()->after('case_permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
            $table->dropColumn('status');
            $table->dropColumn('roles');
            $table->dropColumn('can_be_case_manager');
            $table->dropColumn('case_permissions');
            $table->dropColumn('admin_permissions');
        });
    }
};
