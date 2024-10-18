<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Concerns\RedirectToMonitoring;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditDetails extends EditRecord
{
    use HasParentResource;
    use RedirectToMonitoring;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoringFileEdit($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.edit_details');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.details'));
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->maxWidth('3xl')
                ->schema(self::getSchema())]);
    }

    public static function getSchema(): array
    {
        return [
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
                    DatePicker::make('date')
                        ->label(__('monitoring.labels.date')),

                    TextInput::make('number')
                        ->label(__('monitoring.labels.number'))
                        ->placeholder(__('monitoring.placeholders.number'))
                        ->maxLength(100),

                    DatePicker::make('start_date')
                        ->label(__('monitoring.labels.start_date')),

                    DatePicker::make('end_date')
                        ->label(__('monitoring.labels.end_date')),

                    Hidden::make('parent_id')
                        ->formatStateUsing(fn ($record, $state) => $state ?? ($record?->beneficiary_id ?? request('parent'))),

                    //TODO refactoring after roles implementation
                    Select::make('specialists')
                        ->label(__('monitoring.labels.team'))
                        ->placeholder(__('monitoring.placeholders.team'))
                        ->columnSpanFull()
                        ->preload()
                        ->relationship('specialists')
                        ->multiple()
                        ->options(
                            fn (Get $get) => Beneficiary::find($get('parent_id'))
                                ->team
                                ->each(fn ($item) => $item->full_name = $item->user->getFilamentName())
                                ->pluck('full_name', 'id')
                        ),

                ]),

        ];
    }
}
