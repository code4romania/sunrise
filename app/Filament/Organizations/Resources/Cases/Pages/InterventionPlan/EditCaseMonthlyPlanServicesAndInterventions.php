<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Models\Beneficiary;
use App\Models\MonthlyPlan;
use App\Models\Service;
use App\Models\ServiceIntervention;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class EditCaseMonthlyPlanServicesAndInterventions extends EditRecord
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
        abort_unless(CaseResource::canEdit($this->resolveCaseBeneficiary()), 403);
    }

    protected function resolveCaseBeneficiary(): Beneficiary
    {
        if ($this->beneficiary instanceof Beneficiary) {
            return $this->beneficiary;
        }

        $monthlyPlan = $this->getRecord();
        if (! $monthlyPlan instanceof MonthlyPlan) {
            abort(404);
        }

        $monthlyPlan->loadMissing(['beneficiary', 'interventionPlan.beneficiary']);

        $beneficiary = $monthlyPlan->beneficiary ?? $monthlyPlan->interventionPlan?->beneficiary;
        if (! $beneficiary instanceof Beneficiary) {
            abort(404);
        }

        return $beneficiary;
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.edit_monthly_plan_services_and_interventions_title');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->resolveCaseBeneficiary();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record->getBreadcrumb(),
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            CaseResource::getUrl('view_monthly_plan', ['record' => $record, 'monthlyPlan' => $this->getRecord()]) => __('intervention_plan.headings.monthly_plan'),
            '' => __('intervention_plan.headings.edit_monthly_plan_services_and_interventions_title'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_monthly_plan', [
                    'record' => $this->resolveCaseBeneficiary(),
                    'monthlyPlan' => $this->getRecord(),
                    'tab' => '-'.Str::slug(__('intervention_plan.headings.services_and_interventions')).'-tab',
                ])),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return CaseResource::getUrl('view_monthly_plan', [
            'record' => $this->resolveCaseBeneficiary(),
            'monthlyPlan' => $this->getRecord(),
            'tab' => '-'.Str::slug(__('intervention_plan.headings.services_and_interventions')).'-tab',
        ]);
    }

    /**
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     */
    public function getModel(): string
    {
        return MonthlyPlan::class;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('intervention_plan.headings.services_and_interventions'))
                ->schema($this->getFormSchema()),
        ]);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getFormSchema(): array
    {
        return [
            Repeater::make('monthlyPlanServices')
                ->relationship('monthlyPlanServices')
                ->hiddenLabel()
                ->collapsible()
                ->itemLabel(function () {
                    static $index = 0;

                    return __('intervention_plan.headings.service_count', [
                        'number' => ++$index,
                    ]);
                })
                ->addAction(
                    fn (Action $action) => $action
                        ->link()
                        ->label(__('intervention_plan.actions.add_service_repeater'))
                        ->color('primary')
                )
                ->addActionAlignment(Alignment::Left)
                ->schema([
                    Grid::make(2)
                        ->maxWidth('3xl')
                        ->schema([
                            Select::make('service_id')
                                ->label(__('intervention_plan.labels.service_type'))
                                ->placeholder(__('intervention_plan.placeholders.select_service'))
                                ->live()
                                ->options(function (?int $state): array {
                                    $services = Service::query()
                                        ->active()
                                        ->orderBy('sort')
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->all();

                                    if ($state && ! isset($services[$state])) {
                                        $service = Service::query()->find($state);
                                        if ($service) {
                                            $services[$state] = $service->name;
                                        }
                                    }

                                    return $services;
                                })
                                ->required(),

                            TextInput::make('institution')
                                ->label(__('intervention_plan.labels.responsible_institution'))
                                ->placeholder(__('intervention_plan.placeholders.responsible_institution'))
                                ->default(Filament::getTenant()?->name)
                                ->maxLength(200),
                        ]),

                    TextInput::make('responsible_person')
                        ->label(__('intervention_plan.labels.responsible_person'))
                        ->placeholder(__('intervention_plan.placeholders.responsible_person'))
                        ->columnSpanFull()
                        ->maxWidth('3xl')
                        ->maxLength(200),

                    Grid::make(2)
                        ->maxWidth('3xl')
                        ->schema([
                            DatePicker::make('start_date')
                                ->label(__('intervention_plan.labels.monthly_plan_service_interval_start'))
                                ->placeholder('ZZ/LL/AA'),

                            DatePicker::make('end_date')
                                ->label(__('intervention_plan.labels.monthly_plan_service_interval_end'))
                                ->placeholder('ZZ/LL/AA')
                                ->rules([
                                    function (): \Closure {
                                        return function (string $attribute, mixed $value, \Closure $fail, Validator $validator): void {
                                            if (blank($value) || $value === '-') {
                                                return;
                                            }

                                            $startKey = preg_replace('/\.end_date$/', '.start_date', $attribute);
                                            $start = data_get($validator->getData(), $startKey);

                                            if (blank($start) || $start === '-') {
                                                return;
                                            }

                                            try {
                                                if (Carbon::parse($value)->lt(Carbon::parse($start))) {
                                                    $fail(__('intervention_plan.validation.service_end_date_after_start'));
                                                }
                                            } catch (\Throwable) {
                                                return;
                                            }
                                        };
                                    },
                                ])
                                ->validationAttribute(__('intervention_plan.labels.monthly_plan_service_interval_end')),
                        ]),

                    Textarea::make('objective')
                        ->label(__('intervention_plan.labels.service_objective'))
                        ->placeholder(__('intervention_plan.placeholders.service_objective'))
                        ->columnSpanFull()
                        ->maxWidth('3xl')
                        ->rows(4)
                        ->maxLength(2000),

                    Repeater::make('monthlyPlanInterventions')
                        ->relationship('monthlyPlanInterventions')
                        ->hiddenLabel()
                        ->addActionLabel(__('intervention_plan.actions.add_intervention_repeater'))
                        ->addActionAlignment(Alignment::Center)
                        ->columns(4)
                        ->columnSpanFull()
                        ->itemLabel(fn (array $state): ?string => isset($state['service_intervention_id']) && $state['service_intervention_id'] ? (ServiceIntervention::query()->find($state['service_intervention_id'])?->name ?? null) : null)
                        ->schema([
                            Placeholder::make('number')
                                ->label(__('intervention_plan.labels.count'))
                                ->content(function () {
                                    static $index = 0;

                                    return (string) ++$index;
                                }),

                            Select::make('service_intervention_id')
                                ->label(__('intervention_plan.headings.interventions'))
                                ->placeholder(__('intervention_plan.placeholders.select_intervention'))
                                ->helperText(function (Get $get): ?string {
                                    $serviceId = self::resolveMonthlyPlanServiceIdFromForm($get);
                                    if ($serviceId === null) {
                                        return __('intervention_plan.helpers.select_service_first_for_interventions');
                                    }

                                    $service = Service::query()->find($serviceId);
                                    if (! $service) {
                                        return null;
                                    }

                                    if ($service->activeServiceInterventions()->count() === 0) {
                                        return __('intervention_plan.helpers.no_active_interventions_for_service');
                                    }

                                    return null;
                                })
                                ->options(
                                    function (Get $get, ?int $state): array {
                                        $serviceId = self::resolveMonthlyPlanServiceIdFromForm($get);
                                        if ($serviceId === null) {
                                            return [];
                                        }

                                        $service = Service::query()->find($serviceId);
                                        if (! $service) {
                                            return [];
                                        }

                                        $interventions = $service->activeServiceInterventions()
                                            ->orderBy('sort')
                                            ->get()
                                            ->pluck('name', 'id')
                                            ->all();

                                        if ($state && ! isset($interventions[$state])) {
                                            $intervention = ServiceIntervention::query()->find($state);
                                            if ($intervention) {
                                                $interventions[$state] = $intervention->name;
                                            }
                                        }

                                        return $interventions;
                                    }
                                ),

                            TextInput::make('objections')
                                ->label(__('intervention_plan.labels.objectives_short'))
                                ->placeholder(__('intervention_plan.placeholders.monthly_plan_objective'))
                                ->maxLength(200),

                            TextInput::make('observations')
                                ->label(__('intervention_plan.labels.observations'))
                                ->placeholder(__('intervention_plan.placeholders.monthly_plan_observations'))
                                ->maxLength(200),
                        ]),

                    Textarea::make('service_details')
                        ->label(__('intervention_plan.labels.service_details_label'))
                        ->placeholder(__('intervention_plan.placeholders.service_details'))
                        ->columnSpanFull()
                        ->maxWidth('3xl')
                        ->rows(4)
                        ->maxLength(2000),
                ]),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('filament-actions::edit.single.notifications.saved.title'));
    }

    private static function resolveMonthlyPlanServiceIdFromForm(Get $get): ?int
    {
        $get->skipComponentsChildContainersWhileSearching(false);

        try {
            $raw = $get('../../service_id') ?? $get('../service_id');
        } finally {
            $get->skipComponentsChildContainersWhileSearching(true);
        }

        if ($raw === null || $raw === '') {
            return null;
        }

        return (int) $raw;
    }
}
