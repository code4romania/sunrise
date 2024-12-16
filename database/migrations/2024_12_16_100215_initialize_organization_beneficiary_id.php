<?php

declare(strict_types=1);

use App\Models\Beneficiary;
use App\Models\Organization;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Organization::all()->each(function (Organization $organization) {
            $beneficiaryID = 1;
            $organization->beneficiaries()
                ->orderBy('id')
                ->with(['legal_residence', 'effective_residence'])
                ->get()
                ->each(
                    function (Beneficiary $beneficiary) {
                        static $beneficiaryID = 1;
                        $beneficiary->update(['organization_beneficiary_id' => $beneficiaryID++]);
                    }
                );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
