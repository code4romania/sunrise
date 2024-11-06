<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Enums\CounselingSheet;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditCounselingSheet;
use App\Filament\Organizations\Resources\ServiceResource\Pages;
use App\Forms\Components\Notice;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Models\InterventionService;
use App\Models\OrganizationService;
use App\Models\Service;
use App\Models\ServiceIntervention;
use Filament\Actions\StaticAction;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource
{
    protected static ?string $model = OrganizationService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 32;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.configurations._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('service.headings.navigation');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->maxWidth('3xl')
                    ->schema([
                        Placeholder::make('before_form')
                            ->hiddenLabel()
                            ->content(__('service.helper_texts.before_form')),

                        Select::make('service_id')
                            ->label(__('service.labels.name'))
                            ->visible(fn (string $operation) => $operation === 'create')
                            ->relationship(
                                'service',
                                'name',
                                fn (Builder $query, $state) => $query->whereNotIn(
                                    'id',
                                    OrganizationService::query()
                                        ->whereNot('service_id', $state)
                                        ->pluck('service_id')
                                )
                            )
                            ->afterStateUpdated(self::populateTable())
                            ->live(),

                        Placeholder::make('service')
                            ->label(__('service.labels.name'))
                            ->content(fn ($record) => $record->serviceWithoutStatusCondition->name)
                            ->visible(fn (string $operation) => $operation === 'edit'),

                        Notice::make('counseling_sheet_placeholder')
                            ->content(function (Forms\Get $get) {
                                $service = Service::find($get('service_id'));

                                return $service?->counseling_sheet ? __('service.helper_texts.counseling_sheet') : null;
                            })
                            ->icon('heroicon-o-document-text')
                            ->key('counseling_sheet_placeholder')
                            ->visible(fn (Forms\Get $get) => Service::find($get('service_id'))
                                ?->counseling_sheet)
                            ->registerActions([
                                Action::make('view_counseling_sheet')
                                    ->label(__('service.actions.view_counseling_sheet'))
                                    ->modalHeading(
                                        fn (Forms\Get $get) => Service::find($get('service_id'))
                                            ?->counseling_sheet
                                            ?->getLabel()
                                    )
                                    ->form(function (Forms\Get $get) {
                                        $service = Service::find($get('service_id'));

                                        $counselingSheet = $service?->counseling_sheet;

                                        if (! $counselingSheet) {
                                            return null;
                                        }

                                        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::LEGAL_ASSISTANCE)) {
                                            return EditCounselingSheet::getLegalAssistanceForm();
                                        }

                                        if (CounselingSheet::isValue($counselingSheet, CounselingSheet::PSYCHOLOGICAL_ASSISTANCE)) {
                                            return EditCounselingSheet::getSchemaForPsychologicalAssistance();
                                        }

                                        return [];
                                    })
                                    ->disabledForm()
                                    ->modalAutofocus(false)
                                    ->modalSubmitAction(fn (StaticAction $action) => $action->hidden())
                                    ->link(),
                            ]),

                        Group::make()
                            ->visible(fn (Forms\Get $get) => $get('service_id'))
                            ->schema(
                                [
                                    Placeholder::make('counseling_sheet')
                                        ->label(__('service.headings.interventions'))
                                        ->content(__('service.helper_texts.interventions')),

                                    TableRepeater::make('interventions')
                                        ->hideLabels()
                                        ->hiddenLabel()
                                        ->helperText(__('service.helper_texts.under_interventions_table'))
                                        ->relationship('interventions')
                                        ->addAction(fn (Action $action) => $action->hidden())
                                        ->deletable(false)
                                        ->reorderable(false)
                                        ->schema([
                                            Checkbox::make('active')
                                                ->label(__('service.labels.select'))
                                                ->afterStateUpdated(fn (bool $state, Set $set) => $state ? $set('status', true) : $set('status', false))
                                                ->live(),
                                            Placeholder::make('name')
                                                ->label(__('service.labels.interventions'))
                                                ->hiddenLabel()
                                                ->content(fn ($state) => $state),
                                            Toggle::make('status')
                                                ->label(__('service.labels.status'))
                                                ->disabled(fn (Forms\Get $get) => ! $get('active')),
                                            Hidden::make('id'),
                                            Hidden::make('service_intervention_id'),
                                        ])
                                        ->afterStateHydrated(self::populateTable()),
                                ]
                            ),
                    ]),
            ]);
    }

    public static function populateTable(): \Closure
    {
        return function (Set $set, Forms\Get $get) {
            $serviceID = $get('service_id');
            $interventions = ServiceIntervention::query()
                ->where('service_id', $serviceID)
                ->with('organizationIntervention')
                ->active()
                ->get();

            $interventions->map(function (ServiceIntervention $intervention) {
                $intervention->service_intervention_id = $intervention->id;
                $intervention->id = $intervention->organizationIntervention?->id;
                $intervention->status = $intervention->organizationIntervention?->status ?? false;
                $intervention->active = isset($intervention->organizationIntervention) ?: false;
            });

            $set('interventions', $interventions->toArray());
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->with(['serviceWithoutStatusCondition', 'interventionServices.beneficiary'])
                    ->withCount(['interventions'])
            )
            ->columns([
                TextColumn::make('serviceWithoutStatusCondition.name')
                    ->label(__('service.labels.name')),

                TextColumn::make('interventions_count')
                    ->label(__('service.labels.interventions')),

                TextColumn::make('beneficiary')
                    ->label(__('service.labels.cases'))
                    ->default(
                        fn (OrganizationService $record) => $record->interventionServices
                            ->map(fn (InterventionService $item) => $item->beneficiary)
                            ->unique()
                            ->count()
                    ),

                TextColumn::make('status')
                    ->label(__('service.labels.status')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('general.action.view_details')),
            ])
            ->heading(__('service.headings.list_table'))
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('service.actions.create')),
            ])
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->emptyStateHeading(__('service.headings.empty_state_table'))
            ->emptyStateDescription('');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    protected function mutateFormDataBeforeSave()
    {
    }

    public static function processInterventionsBeforeSave(?array $interventions): ?array
    {
        foreach ($interventions as $key => &$intervention) {
            if (! $intervention['active']) {
                unset($interventions[$key]);
                continue;
            }

            $intervention['status'] = (bool) $intervention['status'];
        }

        return $interventions;
    }
}
