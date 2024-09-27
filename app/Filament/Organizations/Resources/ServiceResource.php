<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\ServiceResource\Pages;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use App\Models\OrganizationService;
use App\Models\Service;
use App\Models\ServiceIntervention;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource
{
    protected static ?string $model = OrganizationService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->disabled(fn (string $operation) => $operation === 'edit')
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

                        Placeholder::make('before_interventions')
                            ->hiddenLabel()
                            ->content(function (Forms\Get $get) {
                                $service = Service::find($get('service_id'));

                                return $service?->counseling_sheet ? __('service.helper_texts.counseling_sheet') : null;
                            }),

                        Forms\Components\Group::make()
                            ->visible(fn (Forms\Get $get) => $get('service_id'))
                            ->schema(
                                [
                                    TableRepeater::make('interventions')
                                        ->hideLabels()
                                        ->label(__('service.headings.interventions'))
                                        ->helperText(__('service.helper_texts.interventions'))
                                        ->relationship('interventions')
                                        ->addAction(fn (Forms\Components\Actions\Action $action) => $action->hidden())
                                        ->deletable(false)
                                        ->reorderable(false)
                                        ->schema([
                                            Forms\Components\Checkbox::make('active')
                                                ->label(__('service.labels.select'))
                                                ->live(),
                                            Placeholder::make('name')
                                                ->label(__('service.labels.interventions'))
                                                ->hiddenLabel()
                                                ->content(fn ($state) => $state),
                                            Toggle::make('status')
                                                ->label(__('service.labels.status'))
                                                ->default(false)
                                                ->disabled(
                                                    function (Toggle $component, $get): bool {
                                                        $index = explode('.', $component->getId());
                                                        end($index);
                                                        $index = prev($index);
                                                        $state = $component->getParentRepeater()->getState();
                                                        $state = $state[$index];

                                                        return ! $state['active'];
                                                    }
                                                ),
                                            Forms\Components\Hidden::make('id'),
                                            Forms\Components\Hidden::make('service_intervention_id'),
                                        ])
                                        ->afterStateHydrated(self::populateTable()),
                                ]
                            ),
                    ]),
            ]);
    }

    public static function populateTable(): \Closure
    {
        return function (Forms\Set $set, Forms\Get $get) {
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
                $intervention->active = isset($intervention->organizationIntervention) ?: null;
            });

            $set('interventions', $interventions->toArray());
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service.name')
                    ->label(__('service.labels.name')),
                //                Tables\Columns\TextColumn::make('interventions')
                //->label(__('service.labels.interventions'),
                Tables\Columns\TextColumn::make('cases')
                    ->label(__('service.labels.cases')),
                Tables\Columns\TextColumn::make('status')
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
            ->emptyStateHeading(__('service.headings.empty_state_table'));
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
