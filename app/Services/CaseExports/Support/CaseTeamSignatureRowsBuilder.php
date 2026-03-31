<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Support;

use App\Models\Beneficiary;

class CaseTeamSignatureRowsBuilder
{
    /**
     * @return array<int, array{name:string,role:string,signature:string}>
     */
    public function build(Beneficiary $beneficiary, bool $includeBeneficiary = false): array
    {
        $rows = $beneficiary->specialistsTeam()
            ->with(['user', 'roleForDisplay'])
            ->get()
            ->map(fn ($specialist): array => [
                'name' => (string) ($specialist->user?->full_name ?? ''),
                'role' => (string) ($specialist->roleForDisplay?->name ?? ''),
                'signature' => '',
            ])
            ->values()
            ->all();

        if ($includeBeneficiary) {
            $rows[] = [
                'name' => $beneficiary->full_name,
                'role' => 'Beneficiar',
                'signature' => '',
            ];
        }

        return $rows;
    }
}
