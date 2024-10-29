<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Widgets;

use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Forms\Components\Select;
use App\Models\InterventionPlan;
use App\Models\OrganizationService;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ServicesWidget extends BaseWidget
{
    public ?InterventionPlan $record = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn () => $this->record->services()
                    ->with('organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition', 'user')
                    ->withCount(['beneficiaryInterventions', 'meetings'])
            )
            ->heading(__('intervention_plan.headings.services'))
            ->columns([
                TextColumn::make('organization_service_id')
                    ->label(__('intervention_plan.labels.service'))
                    ->formatStateUsing(fn ($record) => ($record->organizationServiceWithoutStatusCondition->serviceWithoutStatusCondition->name)),
                TextColumn::make('user_id')
                    ->label(__('intervention_plan.labels.specialist'))
                    ->formatStateUsing(fn ($record) => $record->user?->full_name),
                TextColumn::make('beneficiary_interventions_count')
                    ->label(__('intervention_plan.labels.interventions_count')),

                TextColumn::make('meetings_count')
                    ->label(__('intervention_plan.labels.meetings_count')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('intervention_plan.actions.add_service'))
                    ->modalHeading(__('intervention_plan.headings.add_service'))
                    ->form(self::getServiceSchema($this->record->id))
                    ->createAnother(false),
            ])
            ->actions([
                ViewAction::make()
                    ->label(__('general.action.view_details'))
                    ->url(fn ($record) => InterventionPlanResource::getUrl('view_intervention_service', [
                        'parent' => $this->record,
                        'record' => $record,
                    ])),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_service_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_service_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function getServiceSchema(?int $interventionPlanID = null): array
    {
        return [
            Grid::make()
                ->schema([
                    Group::make()
                        ->schema([
                            Select::make('organization_service_id')
                                ->label(__('intervention_plan.labels.service_type'))
                                ->placeholder(__('intervention_plan.placeholders.organization_service'))
                                ->relationship('organizationService')
                                ->options(
                                    OrganizationService::with('service')
                                        ->active()
                                        ->get()
                                        ->filter(fn (OrganizationService $item) => $item->service)
                                        ->pluck('service.name', 'id')
                                ),

                            TextInput::make('institution')
                                ->label(__('intervention_plan.labels.responsible_institution'))
                                ->placeholder(__('intervention_plan.placeholders.institution'))
                                ->default(Filament::getTenant()->name)
                                ->maxLength(100),

                            Select::make('user_id')
                                ->label(__('intervention_plan.labels.responsible_specialist'))
                                ->placeholder(__('intervention_plan.placeholders.specialist'))
                                ->relationship('user')
                                ->options(User::all()->pluck('full_name', 'id')),

                            DatePicker::make('start_date')
                                ->label(__('intervention_plan.labels.start_date'))
                                ->native(false),
                        ]),
                ]),

            Grid::make()
                ->columnSpanFull()
                ->schema([
                    DatePicker::make('start_date_interval')
                        ->label(__('intervention_plan.labels.start_date_interval'))
                        ->native(false),

                    DatePicker::make('end_date_interval')
                        ->label(__('intervention_plan.labels.end_date_interval'))
                        ->native(false),
                ]),

            RichEditor::make('objections')
                ->label(__('intervention_plan.labels.objections'))
                ->placeholder(__('intervention_plan.placeholders.objections'))
                ->maxLength(1000),

            Hidden::make('intervention_plan_id')
                ->default($interventionPlanID),
        ];
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.social_services');
    }
}
