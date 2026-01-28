<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Resources\MonitoringResource\Pages;

use Filament\Schemas\Schema;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Concerns\RedirectToMonitoring;
use App\Filament\Organizations\Resources\BeneficiaryResource\Resources\MonitoringResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Models\Monitoring;
use App\Models\UserRole;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Awcodes\TableRepeater\Header;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditDetails extends EditRecord
{
    use RedirectToMonitoring;
    use PreventSubmitFormOnEnter;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        $parentRecord = $this->getParentRecord();
        return BeneficiaryBreadcrumb::make($parentRecord)->getBreadcrumbsForMonitoringFileEdit($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.edit_details');
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('monitoring.headings.details'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->maxWidth('3xl')
                ->schema($this->getFormSchema()),
        ]);
    }

    public static function getFormSchemaStatic(): array
    {
        $instance = new static();
        $instance->record = new \App\Models\Monitoring();
        return $instance->getFormSchema();
    }

    protected function getFormSchema(): array
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

                    Hidden::make('beneficiary_id')
                        ->formatStateUsing(fn ($record, $state) => $state ?? ($record?->beneficiary_id ?? $this->getParentRecord()?->id)),

                    TableRepeater::make('specialistsTeam')
                        ->relationship('specialistsTeam')
                        ->defaultItems(1)
                        ->hiddenLabel()
                        ->emptyLabel()
                        ->columnSpanFull()
                        ->addActionLabel(__('monitoring.actions.add_specialist'))
                        ->headers([
                            Header::make('id')
                                ->label(__('nomenclature.labels.nr')),

                            Header::make('role_id')
                                ->label(__('monitoring.labels.role')),

                            Header::make('user_id')
                                ->label(__('monitoring.labels.specialist_name')),
                        ])
                        ->schema([
                            Placeholder::make('id')
                                ->label(__('nomenclature.labels.nr'))
                                ->content(function () {
                                    static $index = 1;

                                    return $index++;
                                })
                                ->hiddenLabel(),

                            Select::make('role_id')
                                ->label(__('monitoring.labels.role'))
                                ->options(
                                    UserRole::query()
                                        ->with('role')
                                        ->get()
                                        ->pluck('role.name', 'role.id')
                                )
                                ->live(),

                            Select::make('user_id')
                                ->label(__('monitoring.labels.specialist_name'))
                                ->options(
                                    fn (Get $get) => UserRole::query()
                                        ->where('role_id', $get('role_id'))
                                        ->with('user')
                                        ->get()
                                        ->pluck('user.full_name', 'user.id')
                                ),

                            Hidden::make('specialistable_type')
                                ->formatStateUsing(fn ($state) => (new Monitoring())->getMorphClass()),

                        ]),
                ]),

        ];
    }
}
