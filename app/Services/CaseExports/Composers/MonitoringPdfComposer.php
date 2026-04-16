<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Models\Beneficiary;
use App\Models\Monitoring;
use App\Services\CaseExports\Support\BeneficiaryPdfTableDataBuilder;
use App\Services\CaseExports\Support\ExportDataFormatter;

class MonitoringPdfComposer
{
    /**
     * @var array<int, string>
     */
    private const MEASURE_FIELDS = [
        'protection_measures',
        'health_measures',
        'legal_measures',
        'psychological_measures',
        'aggressor_relationship',
        'others',
    ];

    public function __construct(
        private readonly ExportDataFormatter $formatter,
        private readonly BeneficiaryPdfTableDataBuilder $beneficiaryTables,
    ) {}

    public function composeReportTitle(Monitoring $monitoring, Beneficiary $beneficiary): string
    {
        return __('monitoring.pdf.report_title', [
            'beneficiary' => trim((string) $beneficiary->full_name) !== '' ? $beneficiary->full_name : '—',
            'interval' => $monitoring->interval,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function composeSections(Monitoring $monitoring, Beneficiary $beneficiary): array
    {
        $identity = $this->beneficiaryTables->buildBeneficiaryIdentityTableData($beneficiary);

        $monitoring->loadMissing([
            'specialistsTeam.user',
            'specialistsTeam.roleForDisplay',
            'children',
        ]);

        $teamLines = $monitoring->specialistsTeam
            ->map(fn ($specialist): string => $specialist->name_role)
            ->filter()
            ->values()
            ->implode("\n");

        $detailsRows = [
            ['label' => __('monitoring.labels.date'), 'value' => $this->formatter->toPrintableValue($monitoring->date)],
            ['label' => __('monitoring.labels.number'), 'value' => $this->formatter->toPrintableValue($monitoring->number)],
            ['label' => __('monitoring.labels.start_date'), 'value' => $this->formatter->toPrintableValue($monitoring->start_date)],
            ['label' => __('monitoring.labels.end_date'), 'value' => $this->formatter->toPrintableValue($monitoring->end_date)],
            ['label' => __('monitoring.headings.interval'), 'value' => $monitoring->interval],
            ['label' => __('monitoring.labels.team'), 'value' => $teamLines !== '' ? $teamLines : '—'],
        ];

        $childrenRows = $monitoring->children->map(function ($child): array {
            return [
                'name' => $this->formatter->toPrintableValue($child->name),
                'status' => $this->formatter->toPrintableValue($child->status),
                'age' => $this->formatter->toPrintableValue($child->age),
                'birthdate' => $this->formatter->toPrintableValue($child->birthdate),
                'aggressor_relationship' => $this->formatter->toPrintableValue($child->aggressor_relationship),
                'maintenance_sources' => $this->formatter->toPrintableValue($child->maintenance_sources),
                'location' => $this->formatter->toPrintableValue($child->location),
                'observations' => $this->formatter->toPrintableValue($child->observations),
            ];
        })->values()->all();

        $topGeneralRows = [
            ['label' => __('monitoring.labels.admittance_date'), 'value' => $this->formatter->toPrintableValue($monitoring->admittance_date)],
            ['label' => __('monitoring.labels.admittance_disposition'), 'value' => $this->formatter->toPrintableValue($monitoring->admittance_disposition)],
            ['label' => __('monitoring.labels.services_in_center'), 'value' => $this->formatter->toPrintableValue($monitoring->services_in_center)],
            ['label' => __('monitoring.labels.progress'), 'value' => $this->formatter->toPrintableValue($monitoring->progress)],
            ['label' => __('monitoring.labels.observation'), 'value' => $this->formatter->toPrintableValue($monitoring->observation)],
        ];

        $measureBlocks = [];
        foreach (self::MEASURE_FIELDS as $field) {
            $payload = $monitoring->{$field};
            $payload = is_array($payload) ? $payload : [];

            $measureBlocks[] = [
                'heading' => (string) __('monitoring.headings.'.$field),
                'rows' => [
                    ['label' => __('monitoring.labels.objection'), 'value' => $this->formatter->toPrintableValue($payload['objection'] ?? null)],
                    ['label' => __('monitoring.labels.activity'), 'value' => $this->formatter->toPrintableValue($payload['activity'] ?? null)],
                    ['label' => __('monitoring.labels.conclusion'), 'value' => $this->formatter->toPrintableValue($payload['conclusion'] ?? null)],
                ],
            ];
        }

        return [
            [
                'title' => __('monitoring.pdf.section_beneficiary_identity'),
                'type' => 'initial_evaluation_identity_beneficiary',
                'identity' => $identity,
            ],
            [
                'title' => __('monitoring.pdf.section_sheet_details'),
                'type' => 'monitoring_label_value_table',
                'rows' => $detailsRows,
            ],
            [
                'title' => __('monitoring.pdf.section_children'),
                'type' => 'monitoring_children_table',
                'rows' => $childrenRows,
            ],
            [
                'title' => __('monitoring.pdf.section_general'),
                'type' => 'monitoring_general_grouped',
                'topRows' => $topGeneralRows,
                'measureBlocks' => $measureBlocks,
            ],
        ];
    }
}
