<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;

use App\Concerns\HasParentResource;
use App\Concerns\PreventMultipleSubmit;
//use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\MonthlyPlanResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Forms\Components\TableRepeater;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Contracts\Support\Htmlable;

class CreateMonthlyPlan extends CreateRecord
{
    use HasWizard;
    use HasParentResource;
    use PreventMultipleSubmit;
//    use PreventSubmitFormOnEnter;

    protected static string $resource = MonthlyPlanResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.create_monthly_plan');
    }

    public function getSteps(): array
    {
        return [
            Step::make('details')
                ->label(__('intervention_plan.headings.monthly_plan_details'))
                ->hidden()
                ->schema([
                    Section::make()
                        ->maxWidth('3xl')
                        ->columns()
                        ->schema([
                            DatePicker::make('start_date')
                                ->label(__('intervention_plan.labels.monthly_plan_start_date'))
                                ->required(),

                            DatePicker::make('end_date')
                                ->label(__('intervention_plan.labels.monthly_plan_end_date'))
                                ->required(),

                            Select::make('case_manager_user_id')
                                ->label(__('intervention_plan.labels.case_manager'))
                                ->placeholder(__('intervention_plan.placeholders.specialist'))
                                ->options(
                                    fn () => $this->parent
                                        ->beneficiary
                                        ->specialistsMembers
                                        ->pluck('full_name', 'id')
                                )
                                ->required(),

                            Select::make('specialists')
                                ->label(__('intervention_plan.labels.specialists'))
                                ->placeholder(__('intervention_plan.placeholders.specialists'))
                                ->multiple()
                                ->options(
                                    fn () => $this->parent
                                        ->beneficiary
                                        ->specialistsTeam
                                        ->pluck('name_role', 'id')
                                )
                                ->required(),
                        ]),

                ]),

            Step::make('services_and_interventions')
                ->label(__('intervention_plan.headings.services_and_interventions'))
                ->schema([
                    Repeater::make('monthlyPlanServices')
                        ->relationship('monthlyPlanServices')
                        ->schema([
                            Grid::make()
                                ->maxWidth('3xl')
                                ->schema([
                                    Select::make('service_id')
                                        ->relationship('service', 'name'),

                                    TextInput::make('institution')
                                        ->default(Filament::getTenant()->name),

                                    TextInput::make('responsible_person')
                                        ->columnSpanFull(),

                                    Textarea::make('objections')
                                        ->columnSpanFull(),
                                ]),

                            TableRepeater::make('monthlyPlanInterventions')
                                ->relationship('monthlyPlanInterventions')
                                ->schema([
                                    Select::make('intervention_id'),
                                    TextInput::make('objections'),
                                    TextInput::make('observations'),
                                ]),
                        ]),
                ]),
        ];
    }
}
