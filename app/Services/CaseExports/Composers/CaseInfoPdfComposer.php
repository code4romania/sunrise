<?php

declare(strict_types=1);

namespace App\Services\CaseExports\Composers;

use App\Enums\AggressorRelationship;
use App\Models\Aggressor;
use App\Models\Beneficiary;
use App\Models\BeneficiaryAntecedents;
use App\Models\BeneficiaryDetails;
use App\Models\FlowPresentation;
use App\Services\CaseExports\Support\ExportDataFormatter;
use Illuminate\Support\Collection;

class CaseInfoPdfComposer
{
    public function __construct(
        private readonly ExportDataFormatter $formatter,
    ) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function compose(Beneficiary $beneficiary): array
    {
        $beneficiary->loadMissing([
            'details',
            'antecedents',
            'aggressors',
            'flowPresentation.referringInstitution',
            'flowPresentation.firstCalledInstitution',
            'flowPresentation.institutions',
            'effective_residence.city',
            'effective_residence.county',
            'legal_residence.city',
            'legal_residence.county',
        ]);

        $sections = [];

        $sections[] = [
            'title' => 'Informații generale',
            'type' => 'monitoring_label_value_table',
            'rows' => $this->buildGeneralBeneficiaryRows($beneficiary),
        ];

        $detailsRows = $this->buildBeneficiaryDetailsRows($beneficiary->details);
        if ($detailsRows !== []) {
            $sections[] = [
                'title' => 'Detalii beneficiar',
                'type' => 'monitoring_label_value_table',
                'rows' => $detailsRows,
            ];
        }

        $antecedentRows = $this->buildAntecedentsRows($beneficiary->antecedents);
        if ($antecedentRows !== []) {
            $sections[] = [
                'title' => 'Antecedente',
                'type' => 'monitoring_label_value_table',
                'rows' => $antecedentRows,
            ];
        }

        $aggressors = $beneficiary->aggressors;
        if ($aggressors->isNotEmpty()) {
            $total = $aggressors->count();
            foreach ($aggressors->values() as $index => $aggressor) {
                $title = $total > 1
                    ? __('case.view.aggressor').' ('.($index + 1).' din '.$total.' '.__('case.aggressors_documented').')'
                    : __('case.view.aggressor');

                $sections[] = [
                    'title' => $title,
                    'type' => 'monitoring_label_value_table',
                    'rows' => $this->buildAggressorRows($aggressor),
                ];
            }
        }

        $flowRows = $this->buildFlowPresentationRows($beneficiary->flowPresentation);
        if ($flowRows !== []) {
            $sections[] = [
                'title' => 'Flux prezentare',
                'type' => 'monitoring_label_value_table',
                'rows' => $flowRows,
            ];
        }

        return $sections;
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function buildGeneralBeneficiaryRows(Beneficiary $beneficiary): array
    {
        $rows = [];

        $this->pushRow($rows, __('field.status'), $this->enumLabel($beneficiary->status));
        $this->pushRow($rows, __('field.first_name'), (string) ($beneficiary->first_name ?? ''));
        $this->pushRow($rows, __('field.last_name'), (string) ($beneficiary->last_name ?? ''));
        $this->pushRow($rows, __('field.prior_name'), (string) ($beneficiary->prior_name ?? ''));
        $this->pushRow($rows, __('field.birthdate'), $beneficiary->birthdate?->format('d.m.Y') ?? '');
        $age = $beneficiary->age;
        $this->pushRow($rows, __('field.age'), $age !== null ? "{$age} ani" : '');

        $this->pushRow($rows, __('field.gender'), $this->enumLabel($beneficiary->gender));
        $this->pushRow($rows, __('field.birthplace'), (string) ($beneficiary->birthplace ?? ''));
        $this->pushRow($rows, __('field.cnp'), (string) ($beneficiary->cnp ?? ''));
        $this->pushRow($rows, __('field.civil_status'), $this->enumLabel($beneficiary->civil_status));
        $this->pushRow($rows, __('field.id_type'), $this->formatter->toPrintableValue($beneficiary->id_type));
        $this->pushRow($rows, __('field.id_serial'), (string) ($beneficiary->id_serial ?? ''));
        $this->pushRow($rows, __('field.id_number'), (string) ($beneficiary->id_number ?? ''));
        $this->pushRow($rows, __('field.citizenship'), $this->enumLabel($beneficiary->citizenship));
        $this->pushRow($rows, __('field.ethnicity'), $this->enumLabel($beneficiary->ethnicity));
        $this->pushRow($rows, __('field.primary_phone'), (string) ($beneficiary->primary_phone ?? ''));
        $this->pushRow($rows, __('field.backup_phone'), (string) ($beneficiary->backup_phone ?? ''));
        $this->pushRow($rows, __('field.email'), (string) ($beneficiary->email ?? ''));
        $this->pushRow($rows, __('beneficiary.section.identity.labels.social_media'), (string) ($beneficiary->social_media ?? ''));
        $this->pushRow($rows, __('beneficiary.section.identity.labels.contact_person_name'), (string) ($beneficiary->contact_person_name ?? ''));
        $this->pushRow($rows, __('beneficiary.section.identity.labels.contact_person_phone'), (string) ($beneficiary->contact_person_phone ?? ''));
        $this->pushRow($rows, __('field.contact_notes'), (string) ($beneficiary->contact_notes ?? ''));
        $this->pushRow($rows, __('field.effective_residence_address'), $this->formatAddress($beneficiary));
        $this->pushRow($rows, __('field.legal_residence_address'), $this->formatLegalAddress($beneficiary));
        $sameAsLegal = $beneficiary->same_as_legal_residence;
        $sameAsLegalLabel = $sameAsLegal === null ? '' : ($sameAsLegal ? __('general.yes') : __('general.no'));
        $this->pushRow($rows, __('field.same_as_legal_residence'), $sameAsLegalLabel);
        $this->pushRow($rows, __('field.children_total_count'), $this->scalarString($beneficiary->children_total_count));
        $this->pushRow($rows, __('field.children_care_count'), $this->scalarString($beneficiary->children_care_count));
        $this->pushRow($rows, __('field.children_under_18_care_count'), $this->scalarString($beneficiary->children_under_18_care_count));
        $this->pushRow($rows, __('field.children_18_care_count'), $this->scalarString($beneficiary->children_18_care_count));
        $this->pushRow($rows, __('field.children_accompanying_count'), $this->scalarString($beneficiary->children_accompanying_count));
        $this->pushRow($rows, __('field.children_notes'), (string) ($beneficiary->children_notes ?? ''));
        $this->pushRow($rows, __('field.doesnt_have_children'), $beneficiary->doesnt_have_children === true ? __('general.yes') : ($beneficiary->doesnt_have_children === false ? __('general.no') : ''));
        $this->pushRow($rows, __('field.notes'), (string) ($beneficiary->notes ?? ''));

        $filtered = $this->filterEmptyRowsForDisplay($rows);

        return $filtered !== [] ? $filtered : [
            ['label' => __('field.beneficiary'), 'value' => '—'],
        ];
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function buildBeneficiaryDetailsRows(?BeneficiaryDetails $details): array
    {
        if ($details === null) {
            return [];
        }

        $rows = [];
        $d = $details;

        $this->pushRow($rows, __('field.has_family_doctor'), $this->enumLabel($d->has_family_doctor));
        $this->pushRow($rows, __('field.family_doctor_name'), (string) ($d->family_doctor_name ?? ''));
        $this->pushRow($rows, __('field.family_doctor_contact'), (string) ($d->family_doctor_contact ?? ''));
        $this->pushRow($rows, __('field.family_doctor_address'), (string) ($d->family_doctor_address ?? ''));
        $this->pushRow($rows, __('field.studies'), $this->enumLabel($d->studies));
        $this->pushRow($rows, __('field.occupation'), $this->enumLabel($d->occupation));
        $this->pushRow($rows, __('field.workplace'), (string) ($d->workplace ?? ''));
        $this->pushRow($rows, __('field.income'), $this->enumLabel($d->income));
        $this->pushRow($rows, __('field.net_income'), (string) ($d->net_income ?? ''));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.income_source'), $this->formatEnumCollection($d->income_source));
        $this->pushRow($rows, __('field.elder_care_count'), $this->scalarString($d->elder_care_count));
        $this->pushRow($rows, __('field.homeownership'), $this->enumLabel($d->homeownership));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.health_insurance'), $this->enumLabel($d->health_insurance));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.health_status'), $this->formatEnumCollection($d->health_status));
        $this->pushRow($rows, __('field.psychiatric_history'), $this->enumLabel($d->psychiatric_history));
        $this->pushRow($rows, __('field.psychiatric_history_notes'), (string) ($d->psychiatric_history_notes ?? ''));
        $this->pushRow($rows, __('field.investigations_for_psychiatric_pathology'), $this->enumLabel($d->investigations_for_psychiatric_pathology));
        $this->pushRow($rows, __('field.investigations_observations'), (string) ($d->investigations_observations ?? ''));
        $this->pushRow($rows, __('field.treatment_for_psychiatric_pathology'), $this->enumLabel($d->treatment_for_psychiatric_pathology));
        $this->pushRow($rows, __('field.treatment_observations'), (string) ($d->treatment_observations ?? ''));
        $this->pushRow($rows, __('field.current_contraception'), $this->enumLabel($d->current_contraception));
        $this->pushRow($rows, __('field.observations_contraception'), (string) ($d->observations_contraception ?? ''));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.drug_consumption'), $this->enumLabel($d->drug_consumption));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.drug_types'), $this->formatEnumCollection($d->drug_types));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.other_current_medication'), $this->enumLabel($d->other_current_medication));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.medication_observations'), (string) ($d->medication_observations ?? ''));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.disabilities'), $this->enumLabel($d->disabilities));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.type_of_disability'), $this->formatEnumCollection($d->type_of_disability));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.degree_of_disability'), $this->enumLabel($d->degree_of_disability));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.observations_disability'), (string) ($d->observations_disability ?? ''));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.observations_chronic_diseases'), (string) ($d->observations_chronic_diseases ?? ''));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.observations_degenerative_diseases'), (string) ($d->observations_degenerative_diseases ?? ''));
        $this->pushRow($rows, __('beneficiary.section.personal_information.label.observations_mental_illness'), (string) ($d->observations_mental_illness ?? ''));

        return $this->filterEmptyRowsForDisplay($rows);
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function buildAntecedentsRows(?BeneficiaryAntecedents $antecedents): array
    {
        if ($antecedents === null) {
            return [];
        }

        $rows = [];
        $this->pushRow($rows, __('field.has_police_reports'), $this->enumLabel($antecedents->has_police_reports));
        $this->pushRow($rows, __('field.police_report_count'), $this->scalarString($antecedents->police_report_count));
        $this->pushRow($rows, __('field.has_medical_reports'), $this->enumLabel($antecedents->has_medical_reports));
        $this->pushRow($rows, __('field.medical_report_count'), $this->scalarString($antecedents->medical_report_count));
        $this->pushRow($rows, __('field.antecedents_observations'), (string) ($antecedents->observations ?? ''));

        return $this->filterEmptyRowsForDisplay($rows);
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function buildAggressorRows(Aggressor $aggressor): array
    {
        $rows = [];
        $a = $aggressor;

        $relationshipLabel = '—';
        if ($a->relationship instanceof AggressorRelationship) {
            $relationshipLabel = $a->relationship === AggressorRelationship::OTHER && filled($a->relationship_other)
                ? $a->relationship->getLabel().' ('.$a->relationship_other.')'
                : $a->relationship->getLabel();
        }

        $this->pushRow($rows, __('field.aggressor_relationship'), $relationshipLabel);
        $this->pushRow($rows, __('field.aggressor_age'), $this->scalarString($a->age));
        $this->pushRow($rows, __('field.aggressor_gender'), $this->enumLabel($a->gender));
        $this->pushRow($rows, __('field.aggressor_citizenship'), $this->enumLabel($a->citizenship));
        $this->pushRow($rows, __('field.aggressor_civil_status'), $this->enumLabel($a->civil_status));
        $this->pushRow($rows, __('field.aggressor_studies'), $this->enumLabel($a->studies));
        $this->pushRow($rows, __('field.aggressor_occupation'), $this->enumLabel($a->occupation));
        $this->pushRow($rows, __('field.aggressor_violence_types'), $this->formatEnumCollection($a->violence_types));
        $this->pushRow($rows, __('field.aggressor_has_violence_history'), $this->enumLabel($a->has_violence_history));
        $this->pushRow($rows, __('field.aggressor_has_psychiatric_history'), $this->enumLabel($a->has_psychiatric_history));
        $this->pushRow($rows, __('field.aggressor_psychiatric_history_notes'), (string) ($a->psychiatric_history_notes ?? ''));
        $this->pushRow($rows, __('field.aggressor_has_drug_history'), $this->enumLabel($a->has_drug_history));
        $this->pushRow($rows, __('field.aggressor_drugs'), $this->formatEnumCollection($a->drugs));
        $this->pushRow($rows, __('field.aggressor_legal_history'), $this->formatEnumCollection($a->legal_history));
        $this->pushRow($rows, __('field.aggressor_legal_history_notes'), (string) ($a->legal_history_notes ?? ''));
        $this->pushRow($rows, __('field.has_protection_order'), $this->enumLabel($a->has_protection_order));
        $this->pushRow($rows, __('field.protection_order_notes'), (string) ($a->protection_order_notes ?? ''));
        $this->pushRow($rows, __('field.electronically_monitored'), $this->enumLabel($a->electronically_monitored));
        $this->pushRow($rows, __('field.has_police_reports'), $this->enumLabel($a->has_police_reports));
        $this->pushRow($rows, __('field.police_report_count'), $this->scalarString($a->police_report_count));
        $this->pushRow($rows, __('field.has_medical_reports'), $this->enumLabel($a->has_medical_reports));
        $this->pushRow($rows, __('field.medical_report_count'), $this->scalarString($a->medical_report_count));
        $this->pushRow($rows, __('field.hospitalization_days'), $this->scalarString($a->hospitalization_days));
        $this->pushRow($rows, __('field.hospitalization_observations'), (string) ($a->hospitalization_observations ?? ''));

        return $this->filterEmptyRowsForDisplay($rows);
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private function buildFlowPresentationRows(?FlowPresentation $flow): array
    {
        if ($flow === null) {
            return [];
        }

        $rows = [];
        $this->pushRow($rows, __('field.presentation_mode'), $this->enumLabel($flow->presentation_mode));
        $this->pushRow($rows, __('field.referring_institution'), (string) ($flow->referringInstitution?->name ?? ''));
        $this->pushRow($rows, __('field.referral_mode'), $this->formatEnumCollection($flow->referral_mode));
        $this->pushRow($rows, __('field.notifier'), $this->enumLabel($flow->notifier));
        $this->pushRow($rows, __('field.notifier_other'), (string) ($flow->notifier_other ?? ''));
        $this->pushRow($rows, __('field.notification_mode'), $this->enumLabel($flow->notification_mode));
        $this->pushRow($rows, __('field.act_location'), $this->formatEnumCollection($flow->act_location));
        $this->pushRow($rows, __('field.act_location_other'), (string) ($flow->act_location_other ?? ''));
        $this->pushRow($rows, __('field.first_called_institution'), (string) ($flow->firstCalledInstitution?->name ?? ''));

        $otherInstitutions = $flow->institutions->pluck('name')->filter()->implode(', ');
        $this->pushRow($rows, __('field.other_called_institutions'), $otherInstitutions);

        return $this->filterEmptyRowsForDisplay($rows);
    }

    /**
     * @param  list<array{label: string, value: string}>  $rows
     */
    private function pushRow(array &$rows, string $label, string $value): void
    {
        $rows[] = [
            'label' => $label,
            'value' => $value,
        ];
    }

    /**
     * @param  list<array{label: string, value: string}>  $rows
     * @return list<array{label: string, value: string}>
     */
    private function filterEmptyRowsForDisplay(array $rows): array
    {
        return array_values(array_filter(
            $rows,
            static fn (array $row): bool => trim($row['value']) !== '' && $row['value'] !== '—',
        ));
    }

    private function formatAddress(Beneficiary $record): string
    {
        $addr = $record->effective_residence;
        if ($addr === null) {
            return '';
        }

        $parts = array_filter([
            $addr->address,
            $addr->city?->name,
            $addr->county !== null ? __('field.county').' '.$addr->county->name : null,
        ]);

        return implode(', ', $parts);
    }

    private function formatLegalAddress(Beneficiary $record): string
    {
        $addr = $record->legal_residence;
        if ($addr === null) {
            return '';
        }

        $parts = array_filter([
            $addr->address,
            $addr->city?->name,
            $addr->county !== null ? __('field.county').' '.$addr->county->name : null,
        ]);

        return implode(', ', $parts);
    }

    private function enumLabel(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_object($value) && method_exists($value, 'getLabel')) {
            return $value->getLabel();
        }

        return $this->formatter->toPrintableValue($value);
    }

    /**
     * @param  Collection<int, mixed>|null  $collection
     */
    private function formatEnumCollection(?Collection $collection): string
    {
        if ($collection === null || $collection->isEmpty()) {
            return '';
        }

        return $collection
            ->map(fn (mixed $item): string => $this->enumLabel($item))
            ->filter(static fn (string $s): bool => $s !== '' && $s !== '—')
            ->implode(', ');
    }

    private function scalarString(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return trim((string) $value);
    }
}
