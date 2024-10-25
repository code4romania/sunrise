<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;
use App\Forms\Components\Select;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionService;
use App\Models\OrganizationServiceIntervention;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BeneficiaryInterventionResource extends Resource
{
    protected static ?string $model = BeneficiaryIntervention::class;

    protected static bool $shouldRegisterNavigation = false;

    public static string $parentResource = InterventionServiceResource::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->maxWidth('3xl')
                    ->columns()
                    ->schema(self::getSchema()),
            ]);
    }

    public static function getSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    Group::make()
                        ->schema([
                            Select::make('organization_service_intervention_id')
                                ->label(__('intervention_plan.labels.intervention_type'))
                                ->relationship('interventionService', 'name')
                                ->options(
                                    OrganizationServiceIntervention::with('serviceIntervention')
                                        ->active()
                                        ->get()
                                        ->pluck('serviceIntervention.name', 'id')
                                )
                                ->required(),

                            Select::make('user_id')
                                ->label(__('intervention_plan.labels.responsible_specialist'))
                                ->relationship('user', 'full_name')
                                ->options(User::all()->pluck('full_name', 'id')),

                            DatePicker::make('start_date')
                                ->label(__('intervention_plan.labels.start_date')),
                        ]),
                ]),

            Grid::make()
                ->schema([
                    DatePicker::make('start_date_interval')
                        ->label(__('intervention_plan.labels.start_date_interval'))
                        ->native(false),

                    DatePicker::make('end_date_interval')
                        ->label(__('intervention_plan.labels.end_date_interval'))
                        ->native(false),
                ]),

            Section::make(__('intervention_plan.headings.intervention_indicators'))
                ->collapsible()
                ->collapsed()
                ->schema([
                    Textarea::make('objections')
                        ->label(__('intervention_plan.labels.objections'))
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    Textarea::make('expected_results')
                        ->label(__('intervention_plan.labels.expected_results'))
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    Textarea::make('procedure')
                        ->label(__('intervention_plan.labels.procedure'))
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    Textarea::make('indicators')
                        ->label(__('intervention_plan.labels.indicators'))
                        ->maxLength(1000)
                        ->columnSpanFull(),
                    Textarea::make('achievement_degree')
                        ->label(__('intervention_plan.labels.achievement_degree'))
                        ->maxLength(1000)
                        ->columnSpanFull(),
                ]),

        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeneficiaryInterventions::route('/'),
            //            'create' => Pages\CreateBeneficiaryIntervention::route('/create'),
            //            'edit' => Pages\EditBeneficiaryIntervention::route('/{record}/edit'),
        ];
    }

    public static function getGroupPages(InterventionService $parent, BeneficiaryIntervention $record): array
    {
        $params = ['parent' => $parent, 'record' => $record];

        return [
            __('intervention_plan.headings.intervention_meetings') => InterventionServiceResource::getUrl('view_meetings', $params),
            __('intervention_plan.headings.intervention_indicators') => InterventionServiceResource::getUrl('view_intervention', $params),
            __('intervention_plan.headings.unfolded') => InterventionServiceResource::getUrl('list_meetings', $params),
        ];
    }
}
