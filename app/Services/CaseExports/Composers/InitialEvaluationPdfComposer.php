<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Enums\AggravatingFactorsSchema;
use App\Enums\RiskFactorsSchema;
use App\Enums\Ternary;
use App\Enums\VictimPerceptionOfTheRiskSchema;
use App\Enums\ViolenceHistorySchema;
use App\Enums\ViolencesTypesSchema;
use App\Models\Beneficiary;
use App\Services\CaseExports\Support\BeneficiaryPdfTableDataBuilder;
use App\Services\CaseExports\Support\ExportDataFormatter;

class InitialEvaluationPdfComposer
{
    public function __construct(
        private readonly ExportDataFormatter $formatter,
        private readonly BeneficiaryPdfTableDataBuilder $beneficiaryTables,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function composeSections(Beneficiary $beneficiary): array
    {
        $identityBeneficiary = $this->beneficiaryTables->buildBeneficiaryIdentityTableData($beneficiary);
        $childrenSection = $this->beneficiaryTables->buildChildrenTableData($beneficiary);

        $riskFactorsModel = $beneficiary->riskFactors;
        $riskFactorsJson = (array) ($riskFactorsModel?->risk_factors ?? []);

        $riskFactorRows = [];
        $orderedEnumClasses = [
            ViolenceHistorySchema::class,
            ViolencesTypesSchema::class,
            RiskFactorsSchema::class,
            VictimPerceptionOfTheRiskSchema::class,
            AggravatingFactorsSchema::class,
        ];

        foreach ($orderedEnumClasses as $enumClass) {
            foreach ($enumClass::options() as $key => $label) {
                $rawValue = $riskFactorsJson[$key]['value'] ?? null;
                $valueLabel = '—';
                if ($rawValue !== null && $rawValue !== '' && $rawValue !== '-') {
                    $valueLabel = Ternary::tryFrom((int) $rawValue)?->label() ?? (string) $rawValue;
                }

                $description = $riskFactorsJson[$key]['description'] ?? null;
                $descriptionText = $this->formatter->toPrintableValue($description);

                $riskFactorRows[] = [
                    'label' => (string) $label,
                    'value' => $valueLabel,
                    'description' => $descriptionText,
                ];
            }
        }

        $extraRiskFactorRows = $this->formatter->normalizeArray([
            'risk_level' => $riskFactorsModel?->risk_level,
            'extended_family_can_provide' => $riskFactorsModel?->extended_family_can_provide,
            'extended_family_can_not_provide' => $riskFactorsModel?->extended_family_can_not_provide,
            'friends_can_provide' => $riskFactorsModel?->friends_can_provide,
            'friends_can_not_provide' => $riskFactorsModel?->friends_can_not_provide,
        ]);

        return [
            ['title' => 'Evaluare inițială - detalii', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->evaluateDetails?->toArray())],
            [
                'title' => 'DATE DE IDENTITATE ALE SOLICITANTULUI:',
                'type' => 'initial_evaluation_identity_beneficiary',
                'identity' => $identityBeneficiary,
            ],
            [
                'title' => '2. DATE DE IDENTITATE DESPRE COPII:',
                'type' => 'initial_evaluation_children_table',
                'children' => $childrenSection,
            ],
            ['title' => 'Violență', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->violence?->toArray())],
            [
                'title' => 'Factori de risc',
                'type' => 'risk_factors_table',
                'rows' => $riskFactorRows,
                'extraRows' => $extraRiskFactorRows,
            ],
            ['title' => 'Servicii solicitate', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->requestedServices?->toArray())],
            ['title' => 'Situația beneficiarului', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->beneficiarySituation?->toArray())],
        ];
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function composeExtraRows(Beneficiary $beneficiary): array
    {
        return [
            ['label' => 'Data creării evaluării inițiale', 'value' => $beneficiary->evaluateDetails?->created_at?->format('d.m.Y') ?? '—'],
        ];
    }
}
