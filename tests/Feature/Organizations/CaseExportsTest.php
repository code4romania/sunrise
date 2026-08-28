<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIntervention;
use App\Models\CloseFile;
use App\Models\InterventionMeeting;
use App\Models\InterventionPlan;
use App\Models\InterventionService;
use App\Models\Monitoring;
use App\Models\MonthlyPlan;
use App\Models\MonthlyPlanInterventions;
use App\Models\MonthlyPlanService;
use App\Models\OrganizationServiceIntervention;
use App\Models\Service;
use App\Models\ServiceIntervention;
use App\Models\User;
use App\Services\CaseExports\CaseExportManager;
use App\Services\CaseExports\Composers\MonthlyPlanSheetPdfComposer;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->withOrganization()->create();
    $this->organization = $this->user->organizations->first();
    $this->actingAs($this->user);
    Filament::setTenant($this->organization);
    Filament::bootCurrentPanel();
    Storage::fake('private');
});

it('generates identity and case info pdf exports', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create([
        'first_name' => 'PdfCaseInfoFirst',
    ]);

    $service = app(CaseExportManager::class);

    $identity = $service->downloadIdentityPdf($beneficiary);
    $caseInfo = $service->downloadCaseInfoPdf($beneficiary);

    expect($identity)->toBeInstanceOf(StreamedResponse::class);
    expect($caseInfo)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $identity->headers->get('content-type'))->toContain('application/pdf');
    expect((string) $caseInfo->headers->get('content-type'))->toContain('application/pdf');
    $storedFiles = Storage::disk('private')->allFiles();
    expect($storedFiles)->not->toBeEmpty();

    $anyPdfContainsCaseInfo = collect($storedFiles)
        ->contains(fn (string $path): bool => str_contains((string) Storage::disk('private')->get($path), 'PdfCaseInfoFirst'));
    expect($anyPdfContainsCaseInfo)->toBeTrue();

    $anyPdfContainsSectionTitle = collect($storedFiles)
        ->contains(fn (string $path): bool => str_contains((string) Storage::disk('private')->get($path), 'Informații generale'));
    expect($anyPdfContainsSectionTitle)->toBeTrue();

    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_identity_exported')
        ->exists())->toBeTrue();

    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_case_info_exported')
        ->exists())->toBeTrue();
});

it('generates monitoring and close-file pdf exports', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $monitoring = Monitoring::factory()->for($beneficiary)->create();
    $closeFile = CloseFile::factory()->for($beneficiary)->create();

    $service = app(CaseExportManager::class);

    $monitoringResponse = $service->downloadMonitoringPdf($monitoring);
    $closeFileResponse = $service->downloadCloseFilePdf($closeFile);

    expect($monitoringResponse)->toBeInstanceOf(StreamedResponse::class);
    expect($closeFileResponse)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $monitoringResponse->headers->get('content-type'))->toContain('application/pdf');
    expect((string) $closeFileResponse->headers->get('content-type'))->toContain('application/pdf');

    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_monitoring_exported')
        ->exists())->toBeTrue();

    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_close_file_exported')
        ->exists())->toBeTrue();

    $closePdfBinary = collect(Storage::disk('private')->allFiles())
        ->map(fn (string $path): string => (string) Storage::disk('private')->get($path))
        ->first(fn (string $binary): bool => str_contains($binary, __('beneficiary.section.close_file.pdf.document_title')));

    expect($closePdfBinary)->not->toBeEmpty();
    expect($closePdfBinary)->toContain(__('beneficiary.section.close_file.pdf.case_manager_role'));
    expect($closePdfBinary)->toContain(__('beneficiary.section.close_file.pdf.admission_reason_heading'));

    $monitoringPdfBinary = collect(Storage::disk('private')->allFiles())
        ->map(fn (string $path): string => (string) Storage::disk('private')->get($path))
        ->first(fn (string $binary): bool => str_contains($binary, 'Fișa de monitorizare a cazului'));

    expect($monitoringPdfBinary)->not->toBeEmpty();
    expect($monitoringPdfBinary)->toContain(__('monitoring.pdf.section_beneficiary_identity'));
    expect($monitoringPdfBinary)->toContain(__('monitoring.pdf.section_sheet_details'));
    expect($monitoringPdfBinary)->toContain(__('monitoring.pdf.section_children'));
    expect($monitoringPdfBinary)->toContain(__('monitoring.pdf.section_general'));
    expect($monitoringPdfBinary)->toContain(__('monitoring.headings.protection_measures'));
});

it('generates meetings csv export with required framing', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $interventionPlan = InterventionPlan::factory()->for($beneficiary)->for($this->organization)->create();
    $interventionService = InterventionService::factory()->for($interventionPlan)->create();
    $organizationServiceIntervention = OrganizationServiceIntervention::factory()->create();
    $beneficiaryIntervention = BeneficiaryIntervention::factory()
        ->for($interventionService)
        ->for($organizationServiceIntervention)
        ->create();

    InterventionMeeting::factory()->for($beneficiaryIntervention)->create([
        'topic' => 'Tema test',
        'observations' => 'Observatie test',
    ]);

    $response = app(CaseExportManager::class)->downloadMeetingsCsv($beneficiaryIntervention, $beneficiary);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $response->headers->get('content-type'))->toContain('text/csv');
});

it('generates monthly plan pdf export', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $interventionPlan = InterventionPlan::factory()->for($beneficiary)->for($this->organization)->create();
    MonthlyPlan::query()->create([
        'intervention_plan_id' => $interventionPlan->id,
        'start_date' => now()->subMonth()->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    $response = app(CaseExportManager::class)->downloadMonthlyPlanPdf($beneficiary);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $response->headers->get('content-type'))->toContain('application/pdf');

    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_monthly_plan_exported')
        ->exists())->toBeTrue();
});

it('generates monthly plan sheet pdf export', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $interventionPlan = InterventionPlan::factory()->for($beneficiary)->for($this->organization)->create();
    $monthlyPlan = MonthlyPlan::query()->create([
        'intervention_plan_id' => $interventionPlan->id,
        'start_date' => now()->subMonth()->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    $response = app(CaseExportManager::class)->downloadMonthlyPlanSheetPdf($monthlyPlan);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $response->headers->get('content-type'))->toContain('application/pdf');

    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_monthly_plan_sheet_exported')
        ->exists())->toBeTrue();

    $files = Storage::disk('private')->allFiles();
    expect($files)->not->toBeEmpty();
    $pdfBinary = (string) Storage::disk('private')->get($files[0]);
    expect($pdfBinary)->toContain(__('intervention_plan.sheet.document_title'));
    expect($pdfBinary)->toContain(__('intervention_plan.sheet.social_benefits'));
});

it('composes monthly plan sheet data from services and interventions', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $interventionPlan = InterventionPlan::factory()->for($beneficiary)->for($this->organization)->create();
    $monthlyPlan = MonthlyPlan::query()->create([
        'intervention_plan_id' => $interventionPlan->id,
        'start_date' => now()->subMonth()->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    $service = Service::query()->create([
        'name' => 'Asistență medicală',
        'identifier' => 'MED',
        'status' => 1,
        'sort' => 1,
    ]);

    $serviceIntervention = ServiceIntervention::query()->create([
        'service_id' => $service->id,
        'name' => 'Consult',
        'identifier' => 'MED_1',
        'status' => 1,
        'sort' => 1,
    ]);

    $monthlyPlanService = MonthlyPlanService::query()->create([
        'monthly_plan_id' => $monthlyPlan->id,
        'service_id' => $service->id,
        'institution' => 'Spitalul X',
        'objective' => 'Obiectiv serviciu unic',
        'service_details' => 'Detalii suplimentare serviciu',
        'responsible_person' => 'Dr. Ion',
        'start_date' => now()->subWeek()->toDateString(),
        'end_date' => now()->toDateString(),
    ]);

    MonthlyPlanInterventions::query()->create([
        'monthly_plan_service_id' => $monthlyPlanService->id,
        'service_intervention_id' => $serviceIntervention->id,
        'objections' => 'Obiective intervenție lunare',
        'observations' => 'Observații intervenție lunare',
    ]);

    $payload = app(MonthlyPlanSheetPdfComposer::class)->compose($monthlyPlan->fresh());

    expect($payload['service_rows'])->toHaveCount(1);

    $medServiceRow = collect($payload['service_rows'])->firstWhere('identifier', 'MED');
    expect($medServiceRow)->not->toBeNull()
        ->and($medServiceRow['institution'])->toContain('Spitalul X')
        ->and($medServiceRow['objectives'])->toContain('Obiectiv serviciu unic')
        ->and($medServiceRow['objectives'])->toContain('Detalii suplimentare serviciu');

    $medInterventionRow = collect($payload['intervention_rows'])->first(
        fn (array $row): bool => ($row['label'] ?? '') === __('intervention_plan.sheet.intervention_row.MED')
    );
    expect($medInterventionRow)->not->toBeNull()
        ->and($medInterventionRow['objectives'])->toContain('Consult')
        ->and($medInterventionRow['objectives'])->toContain('Obiective intervenție lunare')
        ->and($medInterventionRow['observations'])->toContain('Observații intervenție lunare');
});

it('generates psychological counseling sheet from interventions and meetings', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $interventionPlan = InterventionPlan::factory()->for($beneficiary)->for($this->organization)->create();

    $service = Service::query()->create([
        'name' => 'Asistență psihologică',
        'identifier' => 'PSI',
        'counseling_sheet' => \App\Enums\CounselingSheet::PSYCHOLOGICAL_ASSISTANCE,
        'status' => 1,
        'sort' => 1,
    ]);

    $organizationService = \App\Models\OrganizationService::query()->create([
        'organization_id' => $this->organization->id,
        'service_id' => $service->id,
        'status' => 1,
    ]);

    $interventionService = InterventionService::query()->create([
        'intervention_plan_id' => $interventionPlan->id,
        'organization_service_id' => $organizationService->id,
        'institution' => 'IEESR',
        'objections' => 'Obiective serviciu psihologic',
    ]);

    $serviceIntervention = ServiceIntervention::query()->create([
        'service_id' => $service->id,
        'name' => 'Consiliere individuală',
        'identifier' => 'PSI_1',
        'status' => 1,
        'sort' => 1,
    ]);

    $organizationServiceIntervention = OrganizationServiceIntervention::query()->create([
        'organization_service_id' => $organizationService->id,
        'service_intervention_id' => $serviceIntervention->id,
        'status' => 1,
    ]);

    $beneficiaryInterventionOne = BeneficiaryIntervention::query()->create([
        'intervention_service_id' => $interventionService->id,
        'organization_service_intervention_id' => $organizationServiceIntervention->id,
        'objections' => 'Rezumat intervenție 1',
        'expected_results' => 'Rezultat 1',
        'procedure' => 'Procedură 1',
        'indicators' => 'Indicatori 1',
        'achievement_degree' => 'Grad 1',
    ]);

    $beneficiaryInterventionTwo = BeneficiaryIntervention::query()->create([
        'intervention_service_id' => $interventionService->id,
        'organization_service_intervention_id' => $organizationServiceIntervention->id,
        'objections' => 'Rezumat intervenție 2',
    ]);

    InterventionMeeting::query()->create([
        'beneficiary_intervention_id' => $beneficiaryInterventionOne->id,
        'date' => now()->subDay()->toDateString(),
        'time' => '09:00:00',
        'duration' => 60,
        'topic' => 'Tema ședință 1',
        'observations' => 'Observații ședință 1',
    ]);

    InterventionMeeting::query()->create([
        'beneficiary_intervention_id' => $beneficiaryInterventionTwo->id,
        'date' => now()->toDateString(),
        'time' => '10:00:00',
        'duration' => 45,
        'topic' => 'Tema ședință 2',
        'observations' => 'Observații ședință 2',
    ]);

    $response = app(CaseExportManager::class)->downloadPsychologicalCounselingSheetPdf($interventionService);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $response->headers->get('content-type'))->toContain('application/pdf');

    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_psychological_counseling_sheet_exported')
        ->exists())->toBeTrue();

    $files = Storage::disk('private')->allFiles();
    expect($files)->not->toBeEmpty();
    $pdfBinary = (string) Storage::disk('private')->get($files[0]);
    expect($pdfBinary)->toContain('SECȚIUNEA 27. PLAN DE CONSILIERE / INTERVENȚIE / DETALIERE');
    expect($pdfBinary)->toContain('Tema ședință 1');
    expect($pdfBinary)->toContain('Tema ședință 2');
});

it('generates legal counseling sheet from completed service sheet data', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $interventionPlan = InterventionPlan::factory()->for($beneficiary)->for($this->organization)->create();

    $service = Service::query()->create([
        'name' => 'Asistență juridică',
        'identifier' => 'JRD',
        'counseling_sheet' => \App\Enums\CounselingSheet::LEGAL_ASSISTANCE,
        'status' => 1,
        'sort' => 2,
    ]);

    $organizationService = \App\Models\OrganizationService::query()->create([
        'organization_id' => $this->organization->id,
        'service_id' => $service->id,
        'status' => 1,
    ]);

    $interventionService = InterventionService::query()->create([
        'intervention_plan_id' => $interventionPlan->id,
        'organization_service_id' => $organizationService->id,
        'institution' => 'IEESR',
    ]);

    \App\Models\ServiceCounselingSheet::query()->create([
        'intervention_service_id' => $interventionService->id,
        'data' => [
            'patrimony' => 'apartment',
            'possession_mode' => 'exclusive_property',
            'institutions' => [
                ['institution' => 'Poliție', 'phone' => '0265', 'contact_person' => 'Popescu'],
            ],
            'section_4_details' => [
                ['details' => 'Detalii ședință juridică', 'session_number' => 1, 'schedule_date' => '01.01.2026', 'schedule_time' => '09:00'],
            ],
        ],
    ]);

    $response = app(CaseExportManager::class)->downloadLegalCounselingSheetPdf($interventionService);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $response->headers->get('content-type'))->toContain('application/pdf');
    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_legal_counseling_sheet_exported')
        ->exists())->toBeTrue();

    $files = Storage::disk('private')->allFiles();
    expect($files)->not->toBeEmpty();
    $pdfBinary = (string) Storage::disk('private')->get($files[0]);
    expect($pdfBinary)->toContain('FIȘĂ DE CONSILIERE JURIDICĂ');
    expect($pdfBinary)->toContain('Detalii ședință juridică');
    expect($pdfBinary)->toContain('01.01.2026');
    expect($pdfBinary)->toContain('09:00');
});

it('fills legal counseling sheet section 4 schedule from meetings when session details are absent', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $interventionPlan = InterventionPlan::factory()->for($beneficiary)->for($this->organization)->create();

    $service = Service::query()->create([
        'name' => 'Asistență juridică',
        'identifier' => 'JRD2',
        'counseling_sheet' => \App\Enums\CounselingSheet::LEGAL_ASSISTANCE,
        'status' => 1,
        'sort' => 2,
    ]);

    $organizationService = \App\Models\OrganizationService::query()->create([
        'organization_id' => $this->organization->id,
        'service_id' => $service->id,
        'status' => 1,
    ]);

    $interventionService = InterventionService::query()->create([
        'intervention_plan_id' => $interventionPlan->id,
        'organization_service_id' => $organizationService->id,
        'institution' => 'IEESR',
    ]);

    \App\Models\ServiceCounselingSheet::query()->create([
        'intervention_service_id' => $interventionService->id,
        'data' => [
            'patrimony' => 'apartment',
            'possession_mode' => 'exclusive_property',
        ],
    ]);

    $serviceIntervention = ServiceIntervention::query()->create([
        'service_id' => $service->id,
        'name' => 'Consiliere juridică',
        'identifier' => 'JRD_I1',
        'status' => 1,
        'sort' => 1,
    ]);

    $organizationServiceIntervention = OrganizationServiceIntervention::query()->create([
        'organization_service_id' => $organizationService->id,
        'service_intervention_id' => $serviceIntervention->id,
        'status' => 1,
    ]);

    $beneficiaryIntervention = BeneficiaryIntervention::query()->create([
        'intervention_service_id' => $interventionService->id,
        'organization_service_intervention_id' => $organizationServiceIntervention->id,
    ]);

    InterventionMeeting::query()->create([
        'beneficiary_intervention_id' => $beneficiaryIntervention->id,
        'date' => '2026-03-15',
        'time' => '14:30:00',
        'topic' => 'Discuție dosar',
        'observations' => 'Notițe ședință juridică',
    ]);

    $response = app(CaseExportManager::class)->downloadLegalCounselingSheetPdf($interventionService);

    expect($response)->toBeInstanceOf(StreamedResponse::class);

    $files = Storage::disk('private')->allFiles();
    expect($files)->not->toBeEmpty();
    $pdfBinary = (string) Storage::disk('private')->get($files[0]);
    expect($pdfBinary)->toContain('15.03.2026');
    expect($pdfBinary)->toContain('14:30');
    expect($pdfBinary)->toContain('Notițe ședință juridică');
});

it('generates social counseling sheet with meetings from all service interventions', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();
    $interventionPlan = InterventionPlan::factory()->for($beneficiary)->for($this->organization)->create();

    $service = Service::query()->create([
        'name' => 'Asistență socială',
        'identifier' => 'SOC',
        'counseling_sheet' => \App\Enums\CounselingSheet::SOCIAL_ASSISTANCE,
        'status' => 1,
        'sort' => 3,
    ]);

    $organizationService = \App\Models\OrganizationService::query()->create([
        'organization_id' => $this->organization->id,
        'service_id' => $service->id,
        'status' => 1,
    ]);

    $interventionService = InterventionService::query()->create([
        'intervention_plan_id' => $interventionPlan->id,
        'organization_service_id' => $organizationService->id,
        'institution' => 'IEESR',
    ]);

    \App\Models\ServiceCounselingSheet::query()->create([
        'intervention_service_id' => $interventionService->id,
        'data' => [
            'professional_experience' => 'Experiență socială relevantă',
        ],
    ]);

    $serviceInterventionOne = ServiceIntervention::query()->create([
        'service_id' => $service->id,
        'name' => 'Orientare socială',
        'identifier' => 'SOC_1',
        'status' => 1,
        'sort' => 1,
    ]);

    $serviceInterventionTwo = ServiceIntervention::query()->create([
        'service_id' => $service->id,
        'name' => 'Mediere resurse',
        'identifier' => 'SOC_2',
        'status' => 1,
        'sort' => 2,
    ]);

    $organizationServiceInterventionOne = OrganizationServiceIntervention::query()->create([
        'organization_service_id' => $organizationService->id,
        'service_intervention_id' => $serviceInterventionOne->id,
        'status' => 1,
    ]);

    $organizationServiceInterventionTwo = OrganizationServiceIntervention::query()->create([
        'organization_service_id' => $organizationService->id,
        'service_intervention_id' => $serviceInterventionTwo->id,
        'status' => 1,
    ]);

    $beneficiaryInterventionOne = BeneficiaryIntervention::query()->create([
        'intervention_service_id' => $interventionService->id,
        'organization_service_intervention_id' => $organizationServiceInterventionOne->id,
        'objections' => 'Obiective social 1',
    ]);

    $beneficiaryInterventionTwo = BeneficiaryIntervention::query()->create([
        'intervention_service_id' => $interventionService->id,
        'organization_service_intervention_id' => $organizationServiceInterventionTwo->id,
        'objections' => 'Obiective social 2',
    ]);

    InterventionMeeting::query()->create([
        'beneficiary_intervention_id' => $beneficiaryInterventionOne->id,
        'date' => '2026-04-01',
        'time' => '09:15:00',
        'duration' => 50,
        'topic' => 'Intervenție socială 1',
        'observations' => 'Observații social 1',
    ]);

    InterventionMeeting::query()->create([
        'beneficiary_intervention_id' => $beneficiaryInterventionTwo->id,
        'date' => '2026-04-02',
        'time' => '10:30:00',
        'duration' => 60,
        'topic' => 'Intervenție socială 2',
        'observations' => 'Observații social 2',
    ]);

    $response = app(CaseExportManager::class)->downloadSocialCounselingSheetPdf($interventionService);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $response->headers->get('content-type'))->toContain('application/pdf');
    expect(Activity::query()
        ->whereMorphedTo('subject', $beneficiary)
        ->where('event', 'pdf_social_counseling_sheet_exported')
        ->exists())->toBeTrue();

    $files = Storage::disk('private')->allFiles();
    expect($files)->not->toBeEmpty();
    $pdfBinary = (string) Storage::disk('private')->get($files[0]);
    expect($pdfBinary)->toContain('FIȘĂ SOCIALĂ');
    expect($pdfBinary)->toContain('Intervenție socială 1');
    expect($pdfBinary)->toContain('Intervenție socială 2');
});

it('generates initial evaluation pdf export with identity and risk factors layout', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();

    $response = app(CaseExportManager::class)->downloadInitialEvaluationPdf($beneficiary);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $response->headers->get('content-type'))->toContain('application/pdf');

    $files = Storage::disk('private')->allFiles();
    expect($files)->not->toBeEmpty();

    $pdfBinary = Storage::disk('private')->get($files[0]);

    expect($pdfBinary)->toContain('DATE DE IDENTITATE ALE SOLICITANTULUI');
    expect($pdfBinary)->toContain('DATE DE IDENTITATE DESPRE COPII');
    expect($pdfBinary)->toContain('Factori de risc');
});

it('generates detailed evaluation pdf export with tabular sections', function (): void {
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();

    $response = app(CaseExportManager::class)->downloadDetailedEvaluationPdf($beneficiary);

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $response->headers->get('content-type'))->toContain('application/pdf');

    $files = Storage::disk('private')->allFiles();
    expect($files)->not->toBeEmpty();

    $pdfBinary = Storage::disk('private')->get($files[0]);

    expect($pdfBinary)->toContain('I. Date despre beneficiar');
    expect($pdfBinary)->toContain('II. Date despre copii');
    expect($pdfBinary)->toContain('III. Întrevederi/convorbiri telefonice pentru culegerea informațiilor');
    expect($pdfBinary)->toContain('IV. Specialiști care au colaborat la elaborarea acestei evaluări');
    expect($pdfBinary)->toContain('V. Evaluarea multidisciplinară a situației beneficiarului');
});
