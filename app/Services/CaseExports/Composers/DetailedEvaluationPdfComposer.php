<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Enums\RecommendationService;
use App\Models\Beneficiary;
use App\Services\CaseExports\Support\BeneficiaryPdfTableDataBuilder;
use App\Services\CaseExports\Support\ExportDataFormatter;

class DetailedEvaluationPdfComposer
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
        $childrenSection = $this->beneficiaryTables->buildChildrenTableData($beneficiary);
        $partner = $beneficiary->partner;

        $firstPageData = [
            'beneficiary' => [
                'full_name' => trim((string) $beneficiary->full_name) !== '' ? $beneficiary->full_name : '—',
                'cnp' => $beneficiary->cnp ?? '—',
                'birth' => trim(($beneficiary->birthdate?->format('d.m.Y') ?? '—').' / '.($beneficiary->birthplace ?? '—')),
                'legal_address' => $beneficiary->legal_residence?->address ?? '—',
                'effective_address' => $beneficiary->effective_residence?->address ?? '—',
                'studies' => $this->formatter->toPrintableValue($beneficiary->details?->studies),
                'occupation' => $this->formatter->toPrintableValue($beneficiary->details?->occupation),
                'phone' => $beneficiary->primary_phone ?? '—',
                'observations' => $this->formatter->toPrintableValue($beneficiary->contact_notes),
            ],
            'partner' => [
                'full_name' => trim((string) (($partner?->last_name ?? '').' '.($partner?->first_name ?? ''))) !== ''
                    ? trim((string) (($partner?->last_name ?? '').' '.($partner?->first_name ?? '')))
                    : '—',
                'age' => $partner?->age ?? '—',
                'occupation' => $this->formatter->toPrintableValue($partner?->occupation),
                'legal_address' => $partner?->legal_residence?->address ?? '—',
                'effective_address' => $partner?->effective_residence?->address ?? '—',
                'observations' => $this->formatter->toPrintableValue($partner?->observations),
            ],
            'children' => [
                'total' => $childrenSection['total'] ?? 0,
                'accompanying' => $beneficiary->children_accompanying_count ?? 0,
                'rows' => collect($childrenSection['rows'] ?? [])->map(fn (array $row): array => [
                    'name' => $row['name'] ?? '—',
                    'age' => $row['age'] ?? '—',
                    'current_address' => $row['current_address'] ?? '—',
                    'status' => $row['status'] ?? '—',
                    'observations' => $row['observations'] ?? '—',
                ])->values()->all(),
            ],
        ];

        $specialistsRows = $beneficiary->detailedEvaluationSpecialists->map(function ($specialist): array {
            return [
                'full_name' => $specialist->full_name ?? '—',
                'institution' => $specialist->institution ?? '—',
                'relationship' => $specialist->relationship ?? '—',
                'date' => $specialist->date?->format('d.m.Y') ?? '—',
            ];
        })->values()->all();

        $meetingsRows = $beneficiary->meetings->map(function ($meeting): array {
            return [
                'specialist' => $meeting->specialist ?? '—',
                'date' => $meeting->date?->format('d.m.Y') ?? '—',
                'location' => $meeting->location ?? '—',
                'observations' => $this->formatter->toPrintableValue($meeting->observations),
            ];
        })->values()->all();

        $violenceHistoryRows = $beneficiary->violenceHistory->map(function ($history): array {
            return [
                'date' => $history->date_interval ?? '—',
                'significant_events' => $this->formatter->toPrintableValue($history->significant_events),
            ];
        })->values()->all();

        $multidisciplinary = $beneficiary->multidisciplinaryEvaluation;
        $result = $beneficiary->detailedEvaluationResult;
        $multidisciplinarySectionData = [
            'reporting_by' => $this->formatter->toPrintableValue($multidisciplinary?->reporting_by),
            'is_reported_by' => $multidisciplinary?->applicant?->value === 'other',
            'is_direct_request' => $multidisciplinary?->applicant?->value === 'beneficiary',
            'violence_history_rows' => $violenceHistoryRows,
            'medical_need' => $this->formatter->toPrintableValue($multidisciplinary?->medical_need),
            'professional_need' => $this->formatter->toPrintableValue($multidisciplinary?->professional_need),
            'emotional_and_psychological_need' => $this->formatter->toPrintableValue($multidisciplinary?->emotional_and_psychological_need),
            'social_economic_need' => $this->formatter->toPrintableValue($multidisciplinary?->social_economic_need),
            'legal_needs' => $this->formatter->toPrintableValue($multidisciplinary?->legal_needs),
            'extended_family' => $this->formatter->toPrintableValue($multidisciplinary?->extended_family),
            'family_social_integration' => $this->formatter->toPrintableValue($multidisciplinary?->family_social_integration),
            'income' => $this->formatter->toPrintableValue($multidisciplinary?->income),
            'community_resources' => $this->formatter->toPrintableValue($multidisciplinary?->community_resources),
            'house' => $this->formatter->toPrintableValue($multidisciplinary?->house),
            'workplace' => $this->formatter->toPrintableValue($multidisciplinary?->workplace),
            'risk' => $this->formatter->toPrintableValue($multidisciplinary?->risk),
            'recommendations_for_intervention_plan' => $this->formatter->toPrintableValue($result?->recommendations_for_intervention_plan),
            'other_services_description' => $this->formatter->toPrintableValue($result?->other_services_description),
            'recommended_psychological' => $this->hasRecommendation($beneficiary, RecommendationService::PSYCHOLOGICAL_ADVICE),
            'recommended_social' => $this->hasRecommendation($beneficiary, RecommendationService::SOCIAL_ADVICE),
            'recommended_legal' => $this->hasAnyRecommendation($beneficiary, [
                RecommendationService::LEGAL_ADVICE,
                RecommendationService::LEGAL_ASSISTANCE,
            ]),
            'recommended_shelter' => $this->hasAnyRecommendation($beneficiary, [
                RecommendationService::TEMPORARY_SHELTER_SERVICES,
                RecommendationService::SECURING_RESIDENTIAL_SPACES,
            ]),
            'recommended_reintegration' => $this->hasAnyRecommendation($beneficiary, [
                RecommendationService::OCCUPATIONAL_PROGRAM_SERVICES,
                RecommendationService::EDUCATIONAL_SERVICES_FOR_CHILDREN,
                RecommendationService::FAMILY_COUNSELING,
            ]),
            'recommended_medical' => $this->hasAnyRecommendation($beneficiary, [
                RecommendationService::MEDICAL_SERVICES,
                RecommendationService::MEDICAL_PAYMENT,
            ]),
            'recommended_other' => $this->hasRecommendation($beneficiary, RecommendationService::OTHER_SERVICES),
            'applicant_full_name' => $beneficiary->full_name ?? '—',
        ];

        return [
            [
                'title' => '',
                'type' => 'detailed_first_page',
                'data' => $firstPageData,
            ],
            [
                'title' => 'III. Întrevederi/convorbiri telefonice pentru culegerea informațiilor',
                'type' => 'detailed_meetings_table',
                'rows' => $meetingsRows,
            ],
            [
                'title' => 'IV. Specialiști care au colaborat la elaborarea acestei evaluări',
                'type' => 'detailed_specialists_table',
                'rows' => $specialistsRows,
            ],
            [
                'title' => 'V. Evaluarea multidisciplinară a situației beneficiarului',
                'type' => 'detailed_multidisciplinary_section',
                'data' => $multidisciplinarySectionData,
            ],
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

    private function hasRecommendation(Beneficiary $beneficiary, RecommendationService $service): bool
    {
        $recommendations = $beneficiary->detailedEvaluationResult?->recommendation_services;
        if ($recommendations === null) {
            return false;
        }

        foreach ($recommendations as $recommendation) {
            if ($recommendation instanceof RecommendationService) {
                if ($recommendation === $service) {
                    return true;
                }

                continue;
            }

            if ((string) $recommendation === $service->value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<RecommendationService>  $services
     */
    private function hasAnyRecommendation(Beneficiary $beneficiary, array $services): bool
    {
        foreach ($services as $service) {
            if ($this->hasRecommendation($beneficiary, $service)) {
                return true;
            }
        }

        return false;
    }
}
