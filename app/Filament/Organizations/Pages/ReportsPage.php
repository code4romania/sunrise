<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages;

use App\Enums\ReportType;
use App\Forms\Components\ReportTable;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Contracts\View\View;

class ReportsPage extends Page
//    implements Tables\Contracts\HasTable
{
//    use InteractsWithTable;

//    protected static ?string $navigationIcon = 'heroicon-o-document-report';
    protected static string $view = 'filament.organizations.pages.reports-page';

    public $report_type;

    public $start_date;

    public $end_date;

    public $show_missing_values;

    public function submit()
    {
        $data = $this->form->getState();
        $this->report_type = $data['report_type'];
        $this->start_date = $data['start_date'];
        $this->end_date = $data['end_date'];
        $this->show_missing_values = $data['show_missing_values'];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make()
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('report_type')
                        ->key('report_type')
                        ->label('Select Option')
                        ->options(ReportType::options())
                        ->searchable()
                        ->live(),

                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->live(),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->default(now())
                        ->live(),

                    Forms\Components\Checkbox::make('show_missing_values')
                        ->label('Show Missing Values')
                        ->live(),
                ]),

            Forms\Components\Section::make()
                ->schema([
                    ReportTable::make()
                        ->reactive()
                        ->setReportType($this->report_type ? ReportType::tryFrom($this->report_type) : null)
                        ->setStartDate($this->start_date)
                        ->setEndDate($this->end_date)
                        ->setShowMissingValue($this->show_missing_values),
                ]),

        ];
    }

    public function render(): View
    {
        return view($this->getView(), [
            'form' => $this->form
                ->schema($this->getFormSchema())
                ->statePath('form'),
        ])
            ->layout($this->getLayout(), [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }
}
