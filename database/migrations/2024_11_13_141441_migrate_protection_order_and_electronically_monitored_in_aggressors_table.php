<?php

declare(strict_types=1);

use App\Models\Aggressor;
use App\Models\BeneficiaryAntecedents;
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
        BeneficiaryAntecedents::query()
            ->with(['beneficiary.aggressors'])
            ->whereNotNull(['has_protection_order',
                'electronically_monitored',
                'protection_order_notes'])
            ->each(
                fn (BeneficiaryAntecedents $beneficiaryAntecedents) => $beneficiaryAntecedents
                    ->beneficiary
                    ->aggressors
                    ->each(
                        fn (Aggressor $aggressor) => $aggressor->update([
                            'has_protection_order' => $beneficiaryAntecedents->has_protection_order,
                            'electronically_monitored' => $beneficiaryAntecedents->electronically_monitored,
                            'protection_order_notes' => $beneficiaryAntecedents->protection_order_notes,
                        ])
                    ),
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aggressors', function (Blueprint $table) {
            //
        });
    }
};
