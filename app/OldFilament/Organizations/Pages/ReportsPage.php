<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages;

use App\Actions\ExportReport;
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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class ReportsPage extends Page implements HasForms, HasInfolists
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?int $navigationSort = 11;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected string $view = 'filament.organizations.pages.reports-page';

    public $report_type;

    public $start_date;

    public $end_date;

    public $show_missing_values;

    public $add_cases_in_monitoring;

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
        return __('navigation.statistics.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('report.title');
    }

    public function submit(): void
    {
        $this->form->getState();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->columns(12)
                ->schema([
                    Select::make('report_type')
                        ->key('report_type')
                        ->label(__('report.labels.report_type'))
                        ->columnSpan(6)
                        ->options(ReportType::options())
                        ->searchable(),

                    DatePicker::make('start_date')
                        ->label(__('report.labels.start_date'))
                        ->default(now()->startOfMonth())
                        ->columnSpan(3)
                        ->maxDate(fn (Get $get) => $get('end_date') ? $get('end_date') : now())
                        ->live(),

                    DatePicker::make('end_date')
                        ->label(__('report.labels.end_date'))
                        ->default(now())
                        ->columnSpan(3)
                        ->minDate(fn (Get $get) => $get('start_date') ?? null)
                        ->maxDate(now())
                        ->live(),

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

    public function mount(): void
    {
        $this->form->fill();
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(fn () => $this->report_type ? __('report.table_heading.'.$this->report_type) : null)
                ->hiddenLabel()
                ->headerActions([
                    ExportReport::make('export_report')
                        ->setReportType($this->report_type)
                        ->setStartDate($this->start_date)
                        ->setEndDate($this->end_date)
                        ->setShowMissingValues($this->show_missing_values)
                        ->setAddCasesInMonitoring($this->add_cases_in_monitoring),
                ])
                ->schema([
                    $this->reportTable(),
                ]),
        ]);
    }

    public function reportTable(): ReportTable
    {
        return ReportTable::make()
            ->setReportType($this->report_type ? ReportType::tryFrom($this->report_type) : null)
            ->setStartDate($this->start_date)
            ->setEndDate($this->end_date)
            ->setShowMissingValue($this->show_missing_values)
            ->setAddCasesInMonitoring($this->add_cases_in_monitoring);
    }

    public function render(): View
    {
        return view($this->getView(), [
            'form' => $this->form
                ->schema($this->getFormSchema()),
            'infolist' => $this->infolist,
        ])
            ->layout($this->getLayout(), [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }
}
