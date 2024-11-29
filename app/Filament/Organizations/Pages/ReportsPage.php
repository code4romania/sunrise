<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages;

use App\Actions\ExportReport;
use App\Enums\ReportType;
use App\Forms\Components\ReportTable;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class ReportsPage extends Page implements Forms\Contracts\HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithFormActions;

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string $view = 'filament.organizations.pages.reports-page';

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

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make()
                ->columns(4)
                ->schema([
                    Select::make('report_type')
                        ->key('report_type')
                        ->label(__('report.labels.report_type'))
                        ->columnSpan(2)
                        ->options(ReportType::options())
                        ->searchable(),

                    DatePicker::make('start_date')
                        ->label(__('report.labels.start_date'))
                        ->default(now()->startOfMonth())
                        ->maxDate(fn (Get $get) => $get('end_date') ? debug($get('end_date')) : now())
                        ->live(),

                    DatePicker::make('end_date')
                        ->label(__('report.labels.end_date'))
                        ->default(now())
                        ->minDate(fn (Get $get) => $get('start_date') ?? null)
                        ->maxDate(now())
                        ->live(),

                    Checkbox::make('add_cases_in_monitoring')
                        ->label(__('report.labels.add_cases_in_monitoring'))
                        ->helperText(__('report.helpers.add_cases_in_monitoring'))
                        ->columnSpan(2),

                    Checkbox::make('show_missing_values')
                        ->label(__('report.labels.show_missing_values'))
                        ->default(true)
                        ->columnSpan(2),
                ]),
        ];
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make(fn () => $this->report_type ? __('report.table_heading.' . $this->report_type) : null)
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
        return  ReportTable::make()
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
