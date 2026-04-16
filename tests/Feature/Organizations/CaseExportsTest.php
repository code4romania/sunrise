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
use App\Models\OrganizationServiceIntervention;
use App\Models\User;
use App\Services\CaseExports\CaseExportManager;
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
    $beneficiary = Beneficiary::factory()->for($this->organization)->create();

    $service = app(CaseExportManager::class);

    $identity = $service->downloadIdentityPdf($beneficiary);
    $caseInfo = $service->downloadCaseInfoPdf($beneficiary);

    expect($identity)->toBeInstanceOf(StreamedResponse::class);
    expect($caseInfo)->toBeInstanceOf(StreamedResponse::class);
    expect((string) $identity->headers->get('content-type'))->toContain('application/pdf');
    expect((string) $caseInfo->headers->get('content-type'))->toContain('application/pdf');
    expect(Storage::disk('private')->allFiles())->not->toBeEmpty();

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
