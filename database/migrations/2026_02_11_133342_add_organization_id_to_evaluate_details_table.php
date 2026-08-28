<?php

declare(strict_types=1);

use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('evaluate_details', function (Blueprint $table) {
            $table->foreignIdFor(Organization::class)->nullable()->after('beneficiary_id')->constrained()->cascadeOnDelete();
        });

        DB::statement('
            UPDATE evaluate_details ed
            INNER JOIN beneficiaries b ON ed.beneficiary_id = b.id
            SET ed.organization_id = b.organization_id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluate_details', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Organization::class);
        });
    }
};
