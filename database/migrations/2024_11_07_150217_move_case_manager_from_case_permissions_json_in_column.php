<?php

use App\Enums\CasePermission;
use App\Models\Role;
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
        foreach (Role::query()->whereJsonContains('case_permissions', 'can_be_case_manager')->get() as $role) {
            $role->case_manager = true;
            $role->case_permissions = $role->case_permissions->filter(fn (CasePermission $casePermission) => $casePermission->value !== 'can_be_case_manager');
            $role->update();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('column', function (Blueprint $table) {
            //
        });
    }
};
