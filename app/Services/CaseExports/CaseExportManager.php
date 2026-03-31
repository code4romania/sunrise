<?php

declare(strict_types=1);

namespace App\Services\CaseExports;

use App\Enums\ActivityDescription;
use App\Models\Activity;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIntervention;
use App\Models\CloseFile;
use App\Models\Monitoring;
use App\Models\MonthlyPlan;
use App\Services\CaseExports\Support\CaseTeamSignatureRowsBuilder;
use App\Services\CaseExports\Support\ExportBrandingResolver;
use App\Services\CaseExports\Support\ExportDataFormatter;
use App\Services\CaseExports\Support\ExportFilenameBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CaseExportManager
{
    public function __construct(
        private readonly ExportFilenameBuilder $filenameBuilder,
        private readonly ExportBrandingResolver $brandingResolver,
        private readonly ExportDataFormatter $formatter,
        private readonly CaseTeamSignatureRowsBuilder $signatureRowsBuilder,
    ) {}

    public function downloadIdentityPdf(Beneficiary $beneficiary): StreamedResponse
    {
        $beneficiary->loadMissing(['children', 'citizenship']);

        $this->logPdfExport($beneficiary, 'pdf_identity_exported');

        $sections = [
            ['title' => 'Identitate beneficiar', 'rows' => $this->formatter->normalizeArray($beneficiary->only([
                'first_name', 'last_name', 'prior_name', 'cnp', 'gender', 'birthplace', 'birthdate', 'civil_status',
                'id_type', 'id_serial', 'id_number', 'primary_phone', 'backup_phone', 'email', 'social_media',
                'contact_person_name', 'contact_person_phone', 'contact_notes', 'children_total_count',
                'children_care_count', 'children_under_18_care_count', 'children_18_care_count',
                'children_accompanying_count', 'children_notes',
            ]))],
            ['title' => 'Identitate copii', 'rows' => $this->formatter->normalizeArray([
                'children' => $beneficiary->children->map(fn ($child) => $child->toArray())->all(),
            ])],
        ];

        return $this->downloadPdf(
            reportTitle: 'Fișa identitate beneficiar',
            caseId: $beneficiary->id,
            sections: $sections,
        );
    }

    public function downloadCaseInfoPdf(Beneficiary $beneficiary): StreamedResponse
    {
        $beneficiary->loadMissing(['details', 'antecedents', 'aggressors', 'flowPresentation']);

        $this->logPdfExport($beneficiary, 'pdf_case_info_exported');

        $sections = [
            ['title' => 'Informații generale', 'rows' => $this->formatter->normalizeArray($beneficiary->toArray())],
            ['title' => 'Detalii beneficiar', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->details?->toArray())],
            ['title' => 'Antecedente', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->antecedents?->toArray())],
            ['title' => 'Agresor', 'rows' => $this->formatter->normalizeArray([
                'aggressors' => $beneficiary->aggressors->map(fn ($aggressor) => $aggressor->toArray())->all(),
            ])],
            ['title' => 'Flux prezentare', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->flowPresentation?->toArray())],
        ];

        return $this->downloadPdf(
            reportTitle: 'Fișa informații caz',
            caseId: $beneficiary->id,
            sections: $sections,
        );
    }

    public function downloadInitialEvaluationPdf(Beneficiary $beneficiary): StreamedResponse
    {
        $beneficiary->loadMissing(['evaluateDetails', 'violence', 'riskFactors', 'requestedServices', 'beneficiarySituation']);

        $this->logPdfExport($beneficiary, 'pdf_initial_evaluation_exported');

        return $this->downloadPdf(
            reportTitle: 'Fișa evaluare inițială',
            caseId: $beneficiary->id,
            sections: [
                ['title' => 'Evaluare inițială - detalii', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->evaluateDetails?->toArray())],
                ['title' => 'Violență', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->violence?->toArray())],
                ['title' => 'Factori de risc', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->riskFactors?->toArray())],
                ['title' => 'Servicii solicitate', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->requestedServices?->toArray())],
                ['title' => 'Situația beneficiarului', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->beneficiarySituation?->toArray())],
            ],
            extraRows: [['label' => 'Data creării evaluării inițiale', 'value' => $beneficiary->evaluateDetails?->created_at?->format('d.m.Y') ?? '—']],
            signatureRows: $this->signatureRowsBuilder->build($beneficiary),
        );
    }

    public function downloadDetailedEvaluationPdf(Beneficiary $beneficiary): StreamedResponse
    {
        $beneficiary->loadMissing(['detailedEvaluationSpecialists', 'meetings', 'partner', 'multidisciplinaryEvaluation', 'detailedEvaluationResult', 'violenceHistory', 'evaluateDetails']);

        $this->logPdfExport($beneficiary, 'pdf_detailed_evaluation_exported');

        return $this->downloadPdf(
            reportTitle: 'Fișa evaluare detaliată',
            caseId: $beneficiary->id,
            sections: [
                ['title' => 'Specialiști evaluare', 'rows' => $this->formatter->normalizeArray(['specialists' => $beneficiary->detailedEvaluationSpecialists->toArray()])],
                ['title' => 'Întâlniri', 'rows' => $this->formatter->normalizeArray(['meetings' => $beneficiary->meetings->toArray()])],
                ['title' => 'Partener', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->partner?->toArray())],
                ['title' => 'Evaluare multidisciplinară', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->multidisciplinaryEvaluation?->toArray())],
                ['title' => 'Rezultate', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->detailedEvaluationResult?->toArray())],
                ['title' => 'Istoric violență', 'rows' => $this->formatter->normalizeArray(['history' => $beneficiary->violenceHistory->toArray()])],
            ],
            extraRows: [['label' => 'Data creării evaluării inițiale', 'value' => $beneficiary->evaluateDetails?->created_at?->format('d.m.Y') ?? '—']],
            signatureRows: $this->signatureRowsBuilder->build($beneficiary),
        );
    }

    public function downloadMonthlyPlanPdf(Beneficiary $beneficiary): StreamedResponse
    {
        $beneficiary->loadMissing([
            'interventionPlan.services.organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition',
            'interventionPlan.benefits.organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition',
            'interventionPlan.results',
            'interventionPlan.monthlyPlans.monthlyPlanServices.monthlyPlanInterventions.serviceIntervention',
        ]);

        $monthlyPlan = $beneficiary->interventionPlan?->monthlyPlans?->sortByDesc('start_date')?->first();
        $periodLabel = $monthlyPlan instanceof MonthlyPlan ? $monthlyPlan->interval : '—';

        $this->logPdfExport($beneficiary, 'pdf_monthly_plan_exported');

        return $this->downloadPdf(
            reportTitle: "Plan de intervenție lunar pentru perioada {$periodLabel}",
            caseId: $beneficiary->id,
            sections: [
                ['title' => 'Plan de intervenție', 'rows' => $this->formatter->normalizeArray((array) $beneficiary->interventionPlan?->toArray())],
                ['title' => 'Servicii sociale', 'rows' => $this->formatter->normalizeArray(['services' => $beneficiary->interventionPlan?->services?->toArray() ?? []])],
                ['title' => 'Beneficii sociale', 'rows' => $this->formatter->normalizeArray(['benefits' => $beneficiary->interventionPlan?->benefits?->toArray() ?? []])],
                ['title' => 'Rezultate', 'rows' => $this->formatter->normalizeArray(['results' => $beneficiary->interventionPlan?->results?->toArray() ?? []])],
                ['title' => 'Planuri lunare', 'rows' => $this->formatter->normalizeArray(['monthly_plans' => $beneficiary->interventionPlan?->monthlyPlans?->toArray() ?? []])],
            ],
            signatureRows: $this->signatureRowsBuilder->build($beneficiary, includeBeneficiary: true),
        );
    }

    public function downloadMonitoringPdf(Monitoring $monitoring): StreamedResponse
    {
        $monitoring->loadMissing(['beneficiary', 'children']);

        if ($monitoring->beneficiary instanceof Beneficiary) {
            $this->logPdfExport($monitoring->beneficiary, 'pdf_monitoring_exported');
        }

        return $this->downloadPdf(
            reportTitle: 'Fișa de monitorizare a cazului pentru perioada '.$monitoring->interval,
            caseId: $monitoring->beneficiary_id,
            sections: [
                ['title' => 'Date monitorizare', 'rows' => $this->formatter->normalizeArray($monitoring->toArray())],
                ['title' => 'Copii', 'rows' => $this->formatter->normalizeArray(['children' => $monitoring->children->toArray()])],
            ],
            signatureRows: $monitoring->beneficiary ? $this->signatureRowsBuilder->build($monitoring->beneficiary) : [],
        );
    }

    public function downloadCloseFilePdf(CloseFile $closeFile): StreamedResponse
    {
        $closeFile->loadMissing(['beneficiary', 'caseManager.user', 'caseManager.roleForDisplay']);

        if ($closeFile->beneficiary instanceof Beneficiary) {
            $this->logPdfExport($closeFile->beneficiary, 'pdf_close_file_exported');
        }

        return $this->downloadPdf(
            reportTitle: 'Fișa de închidere a cazului',
            caseId: $closeFile->beneficiary_id,
            sections: [
                ['title' => 'Date inchidere caz', 'rows' => $this->formatter->normalizeArray($closeFile->toArray())],
            ],
            signatureRows: $closeFile->beneficiary ? $this->signatureRowsBuilder->build($closeFile->beneficiary) : [],
        );
    }

    public function downloadMeetingsCsv(BeneficiaryIntervention $intervention, Beneficiary $beneficiary): StreamedResponse
    {
        $intervention->loadMissing([
            'organizationServiceIntervention.serviceInterventionWithoutStatusCondition',
            'interventionService.organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition',
            'meetings.specialist.user',
            'meetings.specialist.roleForDisplay',
        ]);

        $serviceName = $intervention->interventionService?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? '—';
        $interventionName = $intervention->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—';
        $reportTitle = "Evidența ședințe și activități - {$serviceName} - {$interventionName}";
        $filename = $this->filenameBuilder->build($reportTitle, $beneficiary->id, 'csv');
        $branding = $this->brandingResolver->resolve();

        return response()->streamDownload(function () use ($intervention, $branding, $beneficiary, $reportTitle): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }

            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, [$branding['name']]);
            fputcsv($out, ['Număr caz: '.$beneficiary->id]);
            fputcsv($out, [$reportTitle]);
            fputcsv($out, []);
            fputcsv($out, [
                __('intervention_plan.labels.meet_number'),
                __('intervention_plan.labels.status'),
                __('intervention_plan.labels.date'),
                __('intervention_plan.labels.time'),
                __('intervention_plan.labels.duration'),
                __('intervention_plan.labels.specialist'),
                __('intervention_plan.labels.topics_covered'),
                __('intervention_plan.labels.observations'),
            ]);

            $number = $intervention->meetings->count();
            foreach ($intervention->meetings as $meeting) {
                fputcsv($out, [
                    $number,
                    $meeting->status?->getLabel() ?? '',
                    $meeting->date?->translatedFormat('d/m/Y') ?? '',
                    $meeting->time ? \Carbon\Carbon::parse($meeting->time)->format('H:i') : '',
                    $meeting->duration !== null ? $meeting->duration.' min' : '',
                    $meeting->specialist?->name_role ?? '',
                    $this->formatter->toPrintableValue($meeting->topic),
                    $this->formatter->toPrintableValue($meeting->observations),
                ]);
                $number--;
            }

            fputcsv($out, []);
            fputcsv($out, ['SUNRISE - Management de caz VD - '.now()->format('d/m/Y')]);
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  array<int, array{title:string, rows:array<int, array{label:string,value:string}>}>  $sections
     * @param  array<int, array{label:string,value:string}>  $extraRows
     * @param  array<int, array{name:string,role:string,signature:string}>  $signatureRows
     */
    private function downloadPdf(
        string $reportTitle,
        int|string $caseId,
        array $sections,
        array $extraRows = [],
        array $signatureRows = [],
    ): StreamedResponse {
        $branding = $this->brandingResolver->resolve();
        $filename = $this->filenameBuilder->build($reportTitle, $caseId, 'pdf');
        $binary = Pdf::loadView('exports.layouts.case-report-pdf', [
            'branding' => $branding,
            'reportTitle' => $reportTitle,
            'caseId' => $caseId,
            'sections' => $sections,
            'extraRows' => $extraRows,
            'signatureRows' => $signatureRows,
        ])
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isPhpEnabled', true)
            ->setOption('isFontSubsettingEnabled', false)
            ->setPaper('a4')
            ->output();

        $relativePath = trim((string) config('exports.path', 'exports/case-reports'), '/').'/'.now()->format('Y/m/d').'/'.$filename;
        $disk = $this->exportsDisk();
        $disk->put($relativePath, $binary, ['visibility' => 'private']);

        return response()->streamDownload(function () use ($disk, $relativePath): void {
            $stream = $disk->readStream($relativePath);
            if ($stream === false) {
                return;
            }

            fpassthru($stream);
            fclose($stream);
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    private function logPdfExport(Beneficiary $beneficiary, string $event): void
    {
        $user = auth()->user();
        if ($user === null) {
            return;
        }

        Activity::create([
            'log_name' => 'default',
            'description' => ActivityDescription::RETRIEVED,
            'subject_type' => $beneficiary->getMorphClass(),
            'subject_id' => $beneficiary->id,
            'event' => $event,
            'causer_type' => $user->getMorphClass(),
            'causer_id' => $user->id,
            'properties' => [
                'case_id' => $beneficiary->id,
                'pdf' => $event,
            ],
        ]);
    }

    private function exportsDisk(): Filesystem
    {
        return Storage::disk(config('exports.disk', 'private'));
    }
}
