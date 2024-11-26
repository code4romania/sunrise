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
        Beneficiary::query()
            ->whereNotNull('children_under_10_care_count')
            ->get()
            ->each(function ($beneficiary) {
                $beneficiary->children_10_18_care_count += $beneficiary->children_under_10_care_count;
                $beneficiary->children_under_10_care_count = 0;
                if ($beneficiary->same_as_legal_residence) {
                    $beneficiary->load(['effective_residence', 'legal_residence']);
                }
                $beneficiary->save();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            //
        });
    }
};
