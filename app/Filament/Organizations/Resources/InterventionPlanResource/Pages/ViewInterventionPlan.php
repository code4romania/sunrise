<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\InterventionPlanResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewInterventionPlan extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = InterventionPlanResource::class;

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->record)
            ->getInterventionPlanBreadcrumb();
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.view_page');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('intervention_plan.actions.edit_intervention_plan'))
                ->icon('heroicon-o-pencil')
                ->outlined()
                ->modal()
                ->form([
                    Grid::make()
                        ->relationship('beneficiary')
                        ->schema([
                            TextInput::make('full_name')
                                ->label(__('intervention_plan.labels.beneficiary_name'))
                                ->disabled(),
                            TextInput::make('cnp')
                                ->label(__('intervention_plan.labels.cnp'))
                                ->disabled(),
                            TextInput::make('address')
                                ->label(__('intervention_plan.labels.address'))
                                ->disabled(),
                        ]),

                    Grid::make()
                        ->schema([
                            DatePicker::make('admit_date_in_center')
                                ->label(__('intervention_plan.labels.admit_date'))
                                ->native(false),
                            DatePicker::make('plan_date')
                                ->label(__('intervention_plan.labels.plan_date'))
                                ->native(false),
                            DatePicker::make('last_revise_date')
                                ->label(__('intervention_plan.labels.last_revise_date'))
                                ->native(false),
                        ]),
                ])
                ->modalHeading(__('intervention_plan.headings.edit_intervention_plan')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make(__('intervention_plan.headings.plan_details'))
                ->columns(3)
                ->schema([
                    TextEntry::make('beneficiary.full_name')
                        ->label(__('intervention_plan.labels.full_name')),
                    TextEntry::make('beneficiary.cnp')
                        ->label(__('intervention_plan.labels.cnp')),
                    TextEntry::make('beneficiary.address')
                        ->label(__('intervention_plan.labels.address'))
                        ->formatStateUsing(
                            fn ($record) => $record->beneficiary->legal_residence_address . ', ' .
                                $record->beneficiary->legalResidenceCity->name . ', ' .
                                $record->beneficiary->legalResidenceCounty->name
                        ),
                    TextEntry::make('admit_date_in_center')
                        ->label(__('intervention_plan.labels.admit_date')),
                    TextEntry::make('plan_date')
                        ->label(__('intervention_plan.labels.plan_date')),
                    TextEntry::make('last_revise_date')
                        ->label(__('intervention_plan.labels.last_revise_date')),
                ]),
        ]);
    }

    protected function getFooterWidgets(): array
    {
        return [
            InterventionPlanResource\Widgets\Interventions::class,
        ];
    }
}
