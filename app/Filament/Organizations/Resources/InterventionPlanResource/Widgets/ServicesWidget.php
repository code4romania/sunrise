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
                    ->with('organizationService.service', 'user')
            )
            ->heading(__('intervention_plan.headings.services'))
            ->columns([
                TextColumn::make('organization_service_id')
                    ->label(__('intervention_plan.labels.service'))
                    ->formatStateUsing(fn ($record) => ($record->organizationService?->service->name)),
                TextColumn::make('user_id')
                    ->label(__('intervention_plan.labels.specialist'))
                    ->formatStateUsing(fn ($record) => $record->user?->full_name),
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
            ]);
    }

    public static function getServiceSchema(?int $interventionPlanID = null): array
    {
        return [
            Select::make('organization_service_id')
                ->label(__('intervention_plan.labels.service_type'))
                ->relationship('organizationService')
                ->options(OrganizationService::with('service')->get()->pluck('service.name', 'id')),
            TextInput::make('institution')
                ->label(__('intervention_plan.labels.responsible_institution'))
                ->default(Filament::getTenant()->name),
            Select::make('user_id')
                ->label(__('intervention_plan.labels.responsible_specialist'))
                ->relationship('user')
                ->options(User::all()->pluck('full_name', 'id')),
            DatePicker::make('start_date')
                ->label(__('intervention_plan.labels.start_date')),
            DatePicker::make('end_date')
                ->label(__('intervention_plan.labels.end_date')),
            RichEditor::make('objections')
                ->label(__('intervention_plan.labels.objections')),
            Hidden::make('intervention_plan_id')
                ->default($interventionPlanID),
        ];
    }

    public function getDisplayName(): string
    {
        return __('intervention_plan.headings.social_services');
    }
}
