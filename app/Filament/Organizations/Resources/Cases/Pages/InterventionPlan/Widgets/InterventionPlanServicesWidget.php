<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\InterventionService;
use App\Models\OrganizationService;
use App\Models\Specialist;
use Filament\Facades\Filament;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class InterventionPlanServicesWidget extends TableWidget
{
    public ?Beneficiary $record = null;

    protected static ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $plan = $this->record?->interventionPlan;

        return $table
            ->query(
                $plan
                    ? $plan->services()
                        ->with(['organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition', 'specialist.user', 'specialist.roleForDisplay'])
                        ->withCount(['beneficiaryInterventions', 'meetings'])
                        ->getQuery()
                    : InterventionService::query()->whereRaw('1 = 0')
            )
            ->heading(__('intervention_plan.headings.services'))
            ->columns([
                TextColumn::make('organization_service_id')
                    ->label(__('intervention_plan.labels.service'))
                    ->formatStateUsing(fn (InterventionService $record) => $record->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? '—'),
                TextColumn::make('specialist.name_role')
                    ->label(__('intervention_plan.labels.specialist')),
                TextColumn::make('beneficiary_interventions_count')
                    ->label(__('intervention_plan.labels.interventions_count')),
                TextColumn::make('meetings_count')
                    ->label(__('intervention_plan.labels.meetings_count')),
                TextColumn::make('view_details')
                    ->label('')
                    ->state(__('intervention_plan.actions.view_details'))
                    ->url(fn (InterventionService $record): string => CaseResource::getUrl('view_intervention_service', [
                        'record' => $this->record,
                        'interventionService' => $record->id,
                    ])),
            ])
            ->recordUrl(fn (InterventionService $record): string => CaseResource::getUrl('view_intervention_service', [
                'record' => $this->record,
                'interventionService' => $record->id,
            ]))
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->label(__('intervention_plan.actions.add_service'))
                    ->color('primary')
                    ->modalHeading(__('intervention_plan.headings.add_service'))
                    ->model(InterventionService::class)
                    ->mutateDataUsing(function (array $data): array {
                        $data['intervention_plan_id'] = $this->record?->interventionPlan?->id;

                        return $data;
                    })
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('organization_service_id')
                                    ->label(__('intervention_plan.labels.service_type'))
                                    ->placeholder(__('intervention_plan.placeholders.organization_service'))
                                    ->options(
                                        OrganizationService::with('serviceWithoutStatusCondition')
                                            ->active()
                                            ->get()
                                            ->filter(fn (OrganizationService $item) => $item->serviceWithoutStatusCondition)
                                            ->pluck('serviceWithoutStatusCondition.name', 'id')
                                    )
                                    ->required(),
                                TextInput::make('institution')
                                    ->label(__('intervention_plan.labels.responsible_institution'))
                                    ->placeholder(__('intervention_plan.placeholders.institution'))
                                    ->default(fn () => Filament::getTenant()?->name)
                                    ->maxLength(100),
                                Select::make('specialist_id')
                                    ->label(__('intervention_plan.labels.responsible_specialist'))
                                    ->placeholder(__('intervention_plan.placeholders.specialist'))
                                    ->options(function (): array {
                                        $plan = $this->record?->interventionPlan;
                                        if (! $plan) {
                                            return [];
                                        }

                                        return $plan->beneficiary
                                            ->specialistsTeam()
                                            ->with(['user:id,first_name,last_name', 'roleForDisplay:id,name'])
                                            ->get()
                                            ->mapWithKeys(fn (Specialist $s) => [$s->id => $s->name_role])
                                            ->all();
                                    }),
                            ]),
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                DatePicker::make('start_date_interval')
                                    ->label(__('intervention_plan.labels.start_date_interval')),
                                DatePicker::make('end_date_interval')
                                    ->label(__('intervention_plan.labels.end_date_interval')),
                            ]),
                        RichEditor::make('objections')
                            ->label(__('intervention_plan.labels.objections'))
                            ->placeholder(__('intervention_plan.placeholders.objections'))
                            ->maxLength(1000),
                    ])
                    ->createAnother(false),
            ])
            ->emptyStateHeading(__('intervention_plan.headings.empty_state_service_table'))
            ->emptyStateDescription(__('intervention_plan.labels.empty_state_service_table'))
            ->emptyStateIcon('heroicon-o-document');
    }

    public static function canView(): bool
    {
        return true;
    }
}
