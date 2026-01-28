<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Schemas;

use App\Enums\CounselingSheet;
use App\Filament\Organizations\Resources\InterventionServiceResource\Pages\EditCounselingSheet;
use App\Forms\Components\Notice;
use App\Forms\Components\Select;
use App\Models\InterventionService;
use App\Models\OrganizationService;
use App\Models\Service;
use App\Models\ServiceIntervention;
use Closure;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components(self::getFormComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->with(['serviceWithoutStatusCondition', 'interventionServices.beneficiary'])
                    ->withCount(['interventions'])
            )
            ->columns(self::getTableColumns())
            ->filters([])
            ->recordActions(self::getTableActions())
            ->heading(__('service.headings.list_table'))
            ->headerActions(self::getTableHeaderActions())
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->emptyStateHeading(__('service.headings.empty_state_table'))
            ->emptyStateDescription('');
    }

    public static function getFormComponents(): array
    {
        return [
            Section::make()
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
                        ->live()
                        ->required(),

                    Placeholder::make('service')
                        ->label(__('service.labels.name'))
                        ->content(fn ($record) => $record->serviceWithoutStatusCondition->name)
                        ->visible(fn (string $operation) => $operation === 'edit'),

                    Notice::make('counseling_sheet_placeholder')
                        ->content(function (Get $get) {
                            $service = Service::find($get('service_id'));

                            return $service?->counseling_sheet ? __('service.helper_texts.counseling_sheet') : null;
                        })
                        ->icon('heroicon-o-document-text')
                        ->key('counseling_sheet_placeholder')
                        ->visible(fn (Get $get) => Service::find($get('service_id'))
                            ?->counseling_sheet)
                        ->registerActions([
                            \Filament\Actions\Action::make('view_counseling_sheet')
                                ->label(__('service.actions.view_counseling_sheet'))
                                ->modalHeading(
                                    fn (Get $get) => Service::find($get('service_id'))
                                        ?->counseling_sheet
                                        ?->getLabel()
                                )
                                ->schema(function (Get $get) {
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
                                ->modalSubmitAction(fn (\Filament\Actions\Action $action) => $action->hidden())
                                ->link(),
                        ]),

                    Group::make()
                        ->visible(fn (Get $get) => $get('service_id'))
                        ->schema([
                            Placeholder::make('counseling_sheet')
                                ->label(__('service.headings.interventions'))
                                ->content(__('service.helper_texts.interventions')),

                            Repeater::make('interventions')
                                ->helperText(__('service.helper_texts.under_interventions_table'))
                                ->relationship('interventions')
                                ->addAction(fn (\Filament\Actions\Action $action) => $action->hidden())
                                ->deletable(false)
                                ->reorderable(false)
                                ->mutateRelationshipDataBeforeCreateUsing(function (array $data) {
                                    unset($data['active']);

                                    return $data;
                                })
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
                                        ->disabled(fn (Get $get) => ! $get('active')),

                                    Hidden::make('id'),

                                    Hidden::make('service_intervention_id'),
                                ])
                                ->columns(3)
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                ->collapsible()
                                ->afterStateHydrated(self::populateTable()),
                        ]),
                ]),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
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
        ];
    }

    public static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->label(__('general.action.view_details')),
        ];
    }

    public static function getTableHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('service.actions.create')),
        ];
    }

    public static function populateTable(): Closure
    {
        return function (Set $set, Get $get) {
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

    public static function processInterventionsBeforeSave(?array $interventions): ?array
    {
        foreach ($interventions as $key => &$intervention) {
            if (! $intervention['active']) {
                unset($interventions[$key]);
                continue;
            }

            unset($intervention['active']);
            $intervention['status'] = (bool) $intervention['status'];
        }

        return $interventions;
    }
}
