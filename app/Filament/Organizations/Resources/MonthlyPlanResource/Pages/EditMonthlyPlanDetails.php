<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonthlyPlanResource\Pages;

use App\Actions\BackAction;
use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\MonthlyPlanResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditMonthlyPlanDetails extends EditRecord
{
    use HasParentResource;

    protected static string $resource = MonthlyPlanResource::class;

    public function getBreadcrumbs(): array
    {
        return  InterventionPlanBreadcrumb::make($this->parent)
            ->getViewMonthlyPlan($this->getRecord());
    }

    public function getTitle(): string
    {
        return __('intervention_plan.headings.edit_monthly_plan_title');
    }

    protected function getRedirectUrl(): ?string
    {
        return InterventionPlanResource::getUrl('view_monthly_plan', [
            'parent' => $this->parent,
            'record' => $this->getRecord(),
            'tab' => \sprintf('-%s-tab', $this->getTabSlug()),
        ]);
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('intervention_plan.headings.monthly_plan_details'));
    }

    public function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url($this->getRedirectUrl()),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema($this->parent->beneficiary));
    }

    public static function getSchema(?Beneficiary $beneficiary = null): array
    {
        return [
            Section::make()
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    DatePicker::make('start_date')
                        ->label(__('intervention_plan.labels.monthly_plan_start_date'))
                        ->displayFormat('d-m-Y')
                        ->required(),

                    DatePicker::make('end_date')
                        ->label(__('intervention_plan.labels.monthly_plan_end_date'))
                        ->displayFormat('d-m-Y')
                        ->required(),

                    Select::make('case_manager_user_id')
                        ->label(__('intervention_plan.labels.case_manager'))
                        ->placeholder(__('intervention_plan.placeholders.specialist'))
                        ->options(
                            fn () => $beneficiary
                                ->specialistsMembers
                                ->pluck('full_name', 'id')
                        )
                        ->required(),

                    Select::make('specialists')
                        ->label(__('intervention_plan.labels.specialists'))
                        ->placeholder(__('intervention_plan.placeholders.specialists'))
                        ->multiple()
                        ->options(
                            fn () => $beneficiary
                                ->specialistsTeam
                                ->pluck('name_role', 'id')
                        )
                        ->required(),
                ]),

            Hidden::make('intervention_plan_id'),
        ];
    }
}
