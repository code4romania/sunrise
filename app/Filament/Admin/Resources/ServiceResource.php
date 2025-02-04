<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\CounselingSheet;
use App\Enums\GeneralStatus;
use App\Filament\Admin\Resources\ServiceResource\Pages;
use App\Filament\Admin\Resources\ServiceResource\Pages\CreateService;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Models\Service;
use App\Models\ServiceIntervention;
use Awcodes\TableRepeater\Header;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('service.label.plural');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('nomenclature.titles.list');
    }

    public static function getModelLabel(): string
    {
        return __('service.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('service.label.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->maxWidth('3xl')
                    ->schema([
                        TextInput::make('name')
                            ->label(__('service.field.name'))
                            ->columnSpanFull()
                            ->maxLength(200)
                            ->required(),

                        Select::make('counseling_sheet')
                            ->label(__('nomenclature.labels.counseling_sheet'))
                            ->options(CounselingSheet::options()),

                    ]),

                Section::make()
                    ->schema([
                        TableRepeater::make('serviceInterventions')
                            ->relationship('serviceInterventions')
                            ->label(__('nomenclature.headings.service_intervention'))
                            ->helperText(__('nomenclature.helper_texts.service_interventions'))
                            ->reorderable()
                            ->orderColumn()
                            ->columnSpanFull()
                            ->addActionLabel(__('nomenclature.actions.add_intervention'))
                            ->minItems(1)
                            ->showLabels()
                            ->headers([
                                Header::make('index')
                                    ->label(__('nomenclature.labels.nr'))
                                    ->width('2em')
                                    ->align(Alignment::Right),

                                Header::make('name')
                                    ->label(__('nomenclature.labels.intervention_name'))
                                    ->markAsRequired(),

                                Header::make('status')
                                    ->label(__('nomenclature.labels.status')),
                            ])
                            ->schema([
                                Placeholder::make('index')
                                    ->label(__('nomenclature.labels.nr'))
                                    ->content(function () {
                                        static $index = 1;

                                        return $index++;
                                    })
                                    ->hiddenLabel(),

                                TextInput::make('name')
                                    ->label(__('nomenclature.labels.intervention_name'))
                                    ->hiddenLabel()
                                    ->maxLength(200)
                                    ->required(),

                                Toggle::make('status')
                                    ->live()
                                    ->default(true)
                                    ->afterStateUpdated(function (bool $state) {
                                        if (! $state) {
                                            // TODO: fix this
                                            dd('Modal cu inactivare de hard confirmation');
                                        }
                                    })
                                    ->label(
                                        fn (bool $state) => $state
                                            ? __('nomenclature.labels.active')
                                            : __('nomenclature.labels.inactive')
                                    ),
                            ])
                            ->deleteAction(
                                fn (Action $action) => $action
                                    ->disabled(function (array $arguments, TableRepeater $component, string $operation): bool {
                                        if ($operation === 'create') {
                                            return false;
                                        }

                                        $items = $component->getState();
                                        $currentItem = $items[$arguments['item']];

                                        $serviceIntervention = ServiceIntervention::where('id', $currentItem['id'])
                                            ->withCount('organizationIntervention')
                                            ->first();

                                        if (! $serviceIntervention) {
                                            return false;
                                        }

                                        return  $serviceIntervention->organization_intervention_count > 0;
                                    })
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['organizationServices']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('nomenclature.labels.name'))
                    ->searchable(),

                TextColumn::make('institutions_count')
                    ->label(__('nomenclature.labels.institutions')),

                TextColumn::make('organization_services_count')
                    ->label(__('nomenclature.labels.centers')),

                TextColumn::make('status')
                    ->label(__('nomenclature.labels.status'))
                    ->badge(),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(fn (Service $record) => ServiceResource::getUrl('view', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('nomenclature.actions.add_service'))
                    ->url(self::getUrl('create')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GeneralStatus::options()),
            ])
            ->heading(__('nomenclature.headings.service_table'))
            ->emptyStateHeading(__('nomenclature.labels.empty_state_service_table'))
            ->emptyStateDescription(null)
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
