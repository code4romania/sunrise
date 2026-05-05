<?php

declare(strict_types=1);

use App\Filament\Organizations\Pages\ReportsNewPage;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->withOrganization()->create();
    $this->organization = $this->user->organizations->first();
    $this->user->update([
        'ngo_admin' => true,
        'institution_id' => $this->organization->institution_id,
    ]);
    $this->actingAs($this->user->fresh());
    Filament::setTenant($this->organization);
    Filament::bootCurrentPanel();
});

it('renders new reports page for users with report access', function () {
    $this->get(ReportsNewPage::getUrl(['tenant' => $this->organization]))
        ->assertSuccessful();
});

it('validates that start date is on or before end date on generate', function () {
    livewire(ReportsNewPage::class, ['tenant' => $this->organization])
        ->fillForm([
            'report_feature' => '36',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-01',
        ])
        ->call('submit')
        ->assertHasFormErrors(['start_date']);
});

it('submits without form errors when the date range is valid', function () {
    livewire(ReportsNewPage::class, ['tenant' => $this->organization])
        ->fillForm([
            'report_feature' => '36',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ])
        ->call('submit')
        ->assertHasNoFormErrors();
});

it('shows xls and pdf export actions after generating a report', function () {
    livewire(ReportsNewPage::class, ['tenant' => $this->organization])
        ->fillForm([
            'report_feature' => '36',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ])
        ->call('submit')
        ->assertSee(__('report.actions.export_xls'))
        ->assertSee(__('report.actions.export_pdf'));
});

it('renders statistics pdf header image when branding header exists', function () {
    $headerUrl = 'https://example.test/header.png';

    $html = view('exports.reports.statistics-report-pdf', [
        'title' => 'Raport test',
        'exportPeriodStart' => '2026-01-01',
        'exportPeriodEnd' => '2026-01-31',
        'exportMeta' => null,
        'header' => ['Categorie', 'Total'],
        'firstHeaderElementColSpan' => 1,
        'firstHeaderElementRowSpan' => 1,
        'subHeader' => [],
        'subHeaderKey' => null,
        'verticalHeader' => [],
        'verticalHeaderKey' => 'status',
        'verticalSubHeader' => [],
        'verticalSubHeaderKey' => null,
        'reportData' => collect(),
        'branding' => [
            'name' => 'Organizatie Test',
            'header_url' => $headerUrl,
        ],
    ])->render();

    expect($html)->toContain($headerUrl)
        ->and($html)->toContain('Organizatie Test');
});
