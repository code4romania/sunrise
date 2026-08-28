<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages;

use App\Actions\ExportReport;
use App\Actions\ExportReportPdf;
use App\Enums\ReportType;
use App\Forms\Components\DatePicker;
use App\Forms\Components\ReportTable;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class ReportsNewPage extends Page implements HasForms, HasInfolists
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?int $navigationSort = 12;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected string $view = 'filament.organizations.pages.reports-new-page';

    public ?string $report_feature = null;

    public ?string $start_date = null;

    public ?string $end_date = null;

    public bool $show_missing_values = true;

    public bool $add_cases_in_monitoring = true;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAccessToReports();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.statistics._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.statistics.new_reports');
    }

    public function getTitle(): string|Htmlable
    {
        return __('report.new.title');
    }

    public function submit(): void
    {
        $this->form->getState();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components($this->getFormSchema());
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function infolist(Schema $schema): Schema
    {
        $sections = [];

        foreach ($this->getSelectedReportTypes() as $reportType) {
            $sections[] = Section::make(__('report.table_heading.'.$reportType->value))
                ->hiddenLabel()
                ->headerActions([
                    ExportReport::make('export_xls_'.$reportType->value)
                        ->setReportType($reportType)
                        ->setStartDate($this->start_date)
                        ->setEndDate($this->end_date)
                        ->setShowMissingValues($this->show_missing_values)
                        ->setAddCasesInMonitoring($this->add_cases_in_monitoring),
//                    ExportReportPdf::make('export_pdf_'.$reportType->value)
//                        ->setReportType($reportType)
//                        ->setStartDate($this->start_date)
//                        ->setEndDate($this->end_date)
//                        ->setShowMissingValues($this->show_missing_values)
//                        ->setAddCasesInMonitoring($this->add_cases_in_monitoring),
                ])
                ->schema([
                    $this->reportTable($reportType),
                ]);
        }

        return $schema->components($sections);
    }

    public function render(): View
    {
        return view($this->getView(), [
            'form' => $this->form->schema($this->getFormSchema()),
            'infolist' => $this->infolist,
        ])->layout($this->getLayout(), [
            'livewire' => $this,
            'maxContentWidth' => $this->getMaxContentWidth(),
            ...$this->getLayoutData(),
        ]);
    }

    /**
     * @return array<int, mixed>
     */
    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->columns(12)
                ->schema([
                    Select::make('report_feature')
                        ->label(__('report.new.labels.report_feature'))
                        ->columnSpan(6)
                        ->options([
                            '36' => __('report.new.options.report_36'),
                            '37' => __('report.new.options.report_37'),
                            '38' => __('report.new.options.report_38'),
                            '40_a' => __('report.new.options.report_40_a'),
                            '40_b' => __('report.new.options.report_40_b'),
                            '40_c' => __('report.new.options.report_40_c'),
                        ])
                        ->searchable(),

                    DatePicker::make('start_date')
                        ->label(__('report.labels.start_date'))
                        ->default(now()->startOfMonth())
                        ->columnSpan(3)
                        ->maxDate(now())
                        ->rules(['required', 'date', 'before_or_equal:end_date']),

                    DatePicker::make('end_date')
                        ->label(__('report.labels.end_date'))
                        ->default(now())
                        ->columnSpan(3)
                        ->maxDate(now())
                        ->rules(['required', 'date', 'after_or_equal:start_date']),

                    Checkbox::make('add_cases_in_monitoring')
                        ->hintIcon('heroicon-o-information-circle', __('report.helpers.add_cases_in_monitoring'))
                        ->hintColor('black')
                        ->columnSpan(2)
                        ->extraAttributes([
                            'class' => 'justify-start',
                        ])
                        ->label(__('report.labels.add_cases_in_monitoring'))
                        ->default(true),

                    Checkbox::make('show_missing_values')
                        ->label(__('report.labels.show_missing_values'))
                        ->default(true)
                        ->extraAttributes([
                            'class' => 'ml-16',
                        ])
                        ->columnSpan(4),
                ]),
        ];
    }

    /**
     * @return array<int, ReportType>
     */
    protected function getSelectedReportTypes(): array
    {
        return match ($this->report_feature) {
            '36' => [ReportType::CASES_BY_RESULTS_STATUS],
            '37' => [ReportType::CASES_BY_VIOLENCE_TYPES, ReportType::CASES_BY_SERVICE_TYPES],
            '38' => [ReportType::CASES_BY_VIOLENCE_TYPES_AND_AGE, ReportType::CASES_BY_SERVICE_TYPES_AND_AGE],
            '40_a' => [ReportType::CASES_BY_AGGRESSOR_RISK_FACTORS],
            '40_b' => [ReportType::CASES_BY_VICTIM_RISK_FACTORS],
            '40_c' => [ReportType::CASES_BY_EVALUATION_INITIAL_RISK_FACTORS],
            default => [],
        };
    }

    protected function reportTable(ReportType $reportType): ReportTable
    {
        return ReportTable::make()
            ->setReportType($reportType)
            ->setStartDate($this->start_date)
            ->setEndDate($this->end_date)
            ->setShowMissingValue($this->show_missing_values)
            ->setAddCasesInMonitoring($this->add_cases_in_monitoring);
    }
}
