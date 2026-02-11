<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\MonthlyPlan;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

class EditCaseMonthlyPlanDetails extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = CaseResource::class;

    protected ?Beneficiary $beneficiary = null;

    public function mount(int|string $record): void
    {
        $this->beneficiary = CaseResource::resolveRecordRouteBinding($record);
        if (! $this->beneficiary instanceof Beneficiary) {
            abort(404);
        }

        $plan = $this->beneficiary->interventionPlan;
        if (! $plan) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->beneficiary]));

            return;
        }

        $monthlyPlanId = request()->route('monthlyPlan');
        $monthlyPlanModel = MonthlyPlan::query()
            ->where('intervention_plan_id', $plan->id)
            ->where('id', $monthlyPlanId)
            ->firstOrFail();

        $this->record = $monthlyPlanModel;
        $this->authorizeAccess();
        $this->fillForm();
        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(CaseResource::canEdit($this->beneficiary ?? $this->getRecord()), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.edit_monthly_plan_title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->beneficiary ?? $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            CaseResource::getUrl('view_monthly_plan', ['record' => $record, 'monthlyPlan' => $this->getRecord()]) => __('intervention_plan.headings.monthly_plan'),
            '' => __('intervention_plan.headings.edit_monthly_plan_title'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_monthly_plan', [
                    'record' => $this->beneficiary,
                    'monthlyPlan' => $this->getRecord(),
                    'tab' => '-'.str(\Illuminate\Support\Str::slug(__('intervention_plan.headings.monthly_plan_details')))->append('-tab')->toString(),
                ])),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return CaseResource::getUrl('view_monthly_plan', [
            'record' => $this->beneficiary,
            'monthlyPlan' => $this->getRecord(),
            'tab' => '-'.str(\Illuminate\Support\Str::slug(__('intervention_plan.headings.monthly_plan_details')))->append('-tab')->toString(),
        ]);
    }

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    public function getModel(): string
    {
        return MonthlyPlan::class;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['specialists'] = $data['specialists'] ?? [];
        if ($data['specialists'] instanceof \Illuminate\Support\Collection) {
            $data['specialists'] = $data['specialists']->values()->all();
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['specialists'] = $data['specialists'] ?? [];

        return $data;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('intervention_plan.headings.monthly_plan_details'))
                ->maxWidth('3xl')
                ->schema([
                    DatePicker::make('start_date')
                        ->label(__('intervention_plan.labels.monthly_plan_start_date'))
                        ->required(),
                    DatePicker::make('end_date')
                        ->label(__('intervention_plan.labels.monthly_plan_end_date'))
                        ->required(),
                    Select::make('case_manager_user_id')
                        ->label(__('intervention_plan.headings.case_manager'))
                        ->options(User::getTenantOrganizationUsers()->all())
                        ->placeholder(__('intervention_plan.placeholders.specialist')),
                    Select::make('specialists')
                        ->label(__('intervention_plan.labels.specialists'))
                        ->multiple()
                        ->options(fn (): Collection => $this->beneficiary?->specialistsTeam()->with('user', 'roleForDisplay')->get()->pluck('name_role', 'id') ?? collect())
                        ->placeholder(__('intervention_plan.placeholders.specialists')),
                ]),
        ]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('filament-actions::edit.single.notifications.saved.title'));
    }
}
