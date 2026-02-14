<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Enums\CounselingSheet;
use App\Enums\MeetingStatus;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\InterventionService;
use App\Models\OrganizationServiceIntervention;
use App\Models\ServiceCounselingSheet;
use App\Models\Specialist;
use App\Schemas\CounselingSheetFormSchemas;
use App\Schemas\CounselingSheetInfolistSchemas;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\EmptyState;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ViewCaseInterventionService extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public ?InterventionService $interventionService = null;

    public function mount(int|string $record, int|string|Model|null $interventionService = null): void
    {
        $this->record = $this->resolveRecord($record);

        if (! $this->record instanceof Beneficiary) {
            abort(404);
        }

        $plan = $this->record->interventionPlan;
        if (! $plan) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->record]));

            return;
        }

        $interventionServiceId = $interventionService instanceof Model
            ? $interventionService->getKey()
            : ($interventionService ?? request()->route('interventionService'));

        if (blank($interventionServiceId)) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->record]));

            return;
        }

        $this->interventionService = InterventionService::query()
            ->where('intervention_plan_id', $plan->id)
            ->where((new InterventionService)->getKeyName(), $interventionServiceId)
            ->with([
                'organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition',
                'specialist.user',
                'specialist.roleForDisplay',
            ])
            ->first();

        if (! $this->interventionService) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->record]));

            return;
        }

        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(CaseResource::canView($this->record), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->interventionService?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? __('intervention_plan.headings.services');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            '' => $this->interventionService?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? __('intervention_plan.headings.services'),
        ];
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        $service = $this->interventionService;
        $redirectUrl = CaseResource::getUrl('view_intervention_plan', ['record' => $record]);

        return [
            BackAction::make()
                ->url($redirectUrl),
            DeleteAction::make()
                ->label(__('intervention_plan.actions.delete_service'))
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->record($service)
                ->successRedirectUrl($redirectUrl)
                ->after(function (): void {
                    Notification::make()
                        ->success()
                        ->title(__('filament-actions::delete.single.notifications.deleted.title'))
                        ->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        $service = $this->interventionService;

        return $schema
            ->record($service)
            ->components([
                Tabs::make()
                    ->persistTabInQueryString('intervention-service-tab')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('intervention_plan.headings.service_details'))
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition.name')
                                            ->label(__('intervention_plan.labels.service_type'))
                                            ->placeholder('—'),
                                        TextEntry::make('institution')
                                            ->label(__('intervention_plan.labels.responsible_institution'))
                                            ->placeholder('—'),
                                        TextEntry::make('specialist.name_role')
                                            ->label(__('intervention_plan.labels.responsible_person'))
                                            ->placeholder('—'),
                                        TextEntry::make('interval')
                                            ->label(__('intervention_plan.labels.period_of_provision'))
                                            ->formatStateUsing(fn ($state) => $state ?: '—')
                                            ->placeholder('—'),
                                        TextEntry::make('objections')
                                            ->label(__('intervention_plan.labels.specific_objectives'))
                                            ->columnSpanFull()
                                            ->placeholder('—'),
                                    ]),
                            ]),
                        Tab::make(__('intervention_plan.headings.counseling_sheet'))
                            ->visible(fn () => $this->interventionService?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->counseling_sheet !== null)
                            ->schema([
                                Section::make(__('intervention_plan.headings.counseling_sheet'))
                                    ->schema([
                                        EmptyState::make(__('intervention_plan.headings.counseling_sheet'))
                                            ->description(__('intervention_plan.labels.counseling_sheet_empty'))
                                            ->icon(Heroicon::OutlinedDocumentText)
                                            ->visible(fn () => $this->isCounselingSheetEmpty()),
                                        Section::make(__('intervention_plan.labels.counseling_sheet_completed'))
                                            ->description(fn () => $this->interventionService?->counselingSheet?->updated_at
                                                ? Carbon::parse($this->interventionService->counselingSheet->updated_at)->translatedFormat('d.m.Y H:i')
                                                : null)
                                            ->visible(fn () => ! $this->isCounselingSheetEmpty())
                                            ->relationship('counselingSheet')
                                            ->schema(fn () => $this->getCounselingSheetDisplaySchema()),
                                    ])
                                    ->headerActions([
                                        Action::make('edit_counseling_sheet')
                                            ->label(fn () => $this->isCounselingSheetEmpty()
                                                ? __('intervention_plan.actions.complete_counseling_sheet')
                                                : __('intervention_plan.actions.edit_counseling_sheet'))
                                            ->icon(Heroicon::OutlinedPencilSquare)
                                            ->slideOver()
                                            ->modalHeading(fn () => $this->getCounselingSheetModalHeading())
                                            ->schema(fn () => $this->getCounselingSheetFormSchema())
                                            ->fillForm(fn () => ['data' => $this->interventionService?->counselingSheet?->data ?? []])
                                            ->action(function (array $data): void {
                                                $sheet = $this->interventionService->counselingSheet;
                                                if (! $sheet) {
                                                    $sheet = new ServiceCounselingSheet(['intervention_service_id' => $this->interventionService->getKey()]);
                                                }
                                                $sheet->data = $data['data'] ?? [];
                                                $sheet->save();
                                                Notification::make()
                                                    ->success()
                                                    ->title(__('filament-actions::edit.single.notifications.saved.title'))
                                                    ->send();
                                                $this->redirect(CaseResource::getUrl('view_intervention_service', [
                                                    'record' => $this->record,
                                                    'interventionService' => $this->interventionService,
                                                ]));
                                            }),
                                    ]),
                            ]),
                        Tab::make(__('intervention_plan.headings.interventions'))
                            ->schema([
                                Section::make(__('intervention_plan.headings.interventions'))
                                    ->schema([
                                        View::make('filament.organizations.components.intervention-cards'),
                                    ])
                                    ->headerActions([
                                        Action::make('add_intervention')
                                            ->label(__('intervention_plan.actions.add_intervention'))
                                            ->icon(Heroicon::OutlinedPlus)
                                            ->color('primary')
                                            ->modalHeading(__('intervention_plan.actions.add_intervention'))
                                            ->schema($this->getAddInterventionFormSchema())
                                            ->action(function (array $data): void {
                                                $intervention = $this->interventionService->beneficiaryInterventions()->create([
                                                    'organization_service_intervention_id' => $data['organization_service_intervention_id'],
                                                    'specialist_id' => $data['specialist_id'] ?? null,
                                                    'start_date_interval' => $data['start_date_interval'] ?? null,
                                                    'end_date_interval' => $data['end_date_interval'] ?? null,
                                                    'objections' => $data['objections'] ?? null,
                                                    'expected_results' => $data['expected_results'] ?? null,
                                                    'procedure' => $data['procedure'] ?? null,
                                                    'indicators' => $data['indicators'] ?? null,
                                                    'achievement_degree' => $data['achievement_degree'] ?? null,
                                                ]);
                                                Notification::make()
                                                    ->success()
                                                    ->title(__('filament-actions::create.single.notifications.created.title'))
                                                    ->send();
                                                $this->redirect(CaseResource::getUrl('view_beneficiary_intervention', [
                                                    'record' => $this->record,
                                                    'interventionService' => $this->interventionService,
                                                    'beneficiaryIntervention' => $intervention->getKey(),
                                                ]));
                                            }),
                                    ]),
                            ]),
                        Tab::make(__('intervention_plan.headings.results_obtained'))
                            ->schema([
                                Section::make(__('intervention_plan.headings.results_obtained'))
                                    ->schema([
                                        RepeatableEntry::make('beneficiaryInterventionsResults')
                                            ->hiddenLabel()
                                            ->state(fn () => $service->beneficiaryInterventions()
                                                ->with(['organizationServiceIntervention.serviceInterventionWithoutStatusCondition'])
                                                ->orderByDesc('id')
                                                ->get())
                                            ->placeholder(__('intervention_plan.headings.empty_state_result_table'))
                                            ->table([
                                                TableColumn::make(__('intervention_plan.labels.service_type')),
                                                TableColumn::make(__('intervention_plan.labels.expected_results')),
                                                TableColumn::make(__('intervention_plan.labels.achievement_degree')),
                                                TableColumn::make(__('intervention_plan.labels.procedure')),
                                            ])
                                            ->schema([
                                                TextEntry::make('organizationServiceIntervention.serviceInterventionWithoutStatusCondition.name')
                                                    ->placeholder('—'),
                                                TextEntry::make('expected_results')
                                                    ->placeholder('—')
                                                    ->limit(50),
                                                TextEntry::make('achievement_degree')
                                                    ->placeholder('—'),
                                                TextEntry::make('procedure')
                                                    ->placeholder('—')
                                                    ->limit(30),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getAddInterventionFormSchema(): array
    {
        $service = $this->interventionService;
        $beneficiary = $this->record;
        $organizationServiceId = $service->organization_service_id;

        return [
            Grid::make()
                ->schema([
                    Select::make('organization_service_intervention_id')
                        ->label(__('intervention_plan.labels.intervention_type'))
                        ->options(
                            OrganizationServiceIntervention::with('serviceInterventionWithoutStatusCondition')
                                ->where('organization_service_id', $organizationServiceId)
                                ->active()
                                ->get()
                                ->filter(fn (OrganizationServiceIntervention $osi) => $osi->serviceInterventionWithoutStatusCondition)
                                ->pluck('serviceInterventionWithoutStatusCondition.name', 'id')
                        )
                        ->required(),
                    Select::make('specialist_id')
                        ->label(__('intervention_plan.labels.responsible_person'))
                        ->options(
                            $beneficiary->specialistsTeam()
                                ->with(['user:id,first_name,last_name', 'roleForDisplay:id,name'])
                                ->get()
                                ->mapWithKeys(fn (Specialist $s) => [$s->id => $s->name_role])
                                ->all()
                        )
                        ->placeholder(__('intervention_plan.placeholders.specialist')),
                ]),
            Grid::make()
                ->columns(2)
                ->schema([
                    DatePicker::make('start_date_interval')
                        ->label(__('intervention_plan.labels.period_of_provision').' (început)'),
                    DatePicker::make('end_date_interval')
                        ->label(__('intervention_plan.labels.period_of_provision').' (sfârșit)'),
                ]),
            Section::make(__('intervention_plan.headings.intervention_indicators'))
                ->schema([
                    Textarea::make('objections')
                        ->label(__('intervention_plan.labels.specific_objectives'))
                        ->placeholder(__('intervention_plan.placeholders.add_details'))
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('expected_results')
                        ->label(__('intervention_plan.labels.expected_results'))
                        ->placeholder(__('intervention_plan.placeholders.add_details'))
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('procedure')
                        ->label(__('intervention_plan.labels.procedure'))
                        ->placeholder(__('intervention_plan.placeholders.add_details'))
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('indicators')
                        ->label(__('intervention_plan.labels.indicators'))
                        ->placeholder(__('intervention_plan.placeholders.add_details'))
                        ->rows(3)
                        ->columnSpanFull(),
                    Textarea::make('achievement_degree')
                        ->label(__('intervention_plan.labels.achievement_degree'))
                        ->placeholder(__('intervention_plan.placeholders.add_details'))
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(true),
        ];
    }

    protected function isCounselingSheetEmpty(): bool
    {
        $sheet = $this->interventionService?->counselingSheet;

        return $sheet === null || empty($sheet->data);
    }

    protected function getCounselingSheetModalHeading(): string
    {
        $type = $this->interventionService?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->counseling_sheet;

        return $type instanceof CounselingSheet
            ? __('intervention_plan.headings.edit_counseling_sheet', ['counseling_sheet_name' => strtolower($type->getLabel())])
            : __('intervention_plan.headings.counseling_sheet');
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getCounselingSheetFormSchema(): array
    {
        $type = $this->getCounselingSheetType();

        return $type !== null ? match ($type) {
            CounselingSheet::LEGAL_ASSISTANCE => CounselingSheetFormSchemas::getLegalAssistanceForm(),
            CounselingSheet::PSYCHOLOGICAL_ASSISTANCE => CounselingSheetFormSchemas::getSchemaForPsychologicalAssistance(),
            CounselingSheet::SOCIAL_ASSISTANCE => CounselingSheetFormSchemas::getSchemaForSocialAssistance($this->interventionService),
            default => [],
        } : [];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Section>
     */
    protected function getCounselingSheetDisplaySchema(): array
    {
        $type = $this->getCounselingSheetType();

        return $type !== null ? CounselingSheetInfolistSchemas::getDisplaySchemaFor($type) : [];
    }

    protected function getCounselingSheetType(): ?CounselingSheet
    {
        $type = $this->interventionService?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->counseling_sheet;

        return $type instanceof CounselingSheet ? $type : null;
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getAddMeetingFormSchema(): array
    {
        $service = $this->interventionService;
        $beneficiary = $this->record;
        $organizationServiceId = $service->organization_service_id;

        return [
            Grid::make()
                ->schema([
                    Select::make('organization_service_intervention_id')
                        ->label(__('intervention_plan.labels.intervention_type'))
                        ->options(
                            OrganizationServiceIntervention::with('serviceInterventionWithoutStatusCondition')
                                ->where('organization_service_id', $organizationServiceId)
                                ->active()
                                ->get()
                                ->filter(fn (OrganizationServiceIntervention $osi) => $osi->serviceInterventionWithoutStatusCondition)
                                ->pluck('serviceInterventionWithoutStatusCondition.name', 'id')
                        )
                        ->required(),
                    Select::make('specialist_id')
                        ->label(__('intervention_plan.labels.responsible_specialist'))
                        ->options(
                            $beneficiary->specialistsTeam()
                                ->with(['user:id,first_name,last_name', 'roleForDisplay:id,name'])
                                ->get()
                                ->mapWithKeys(fn (Specialist $s) => [$s->id => $s->name_role])
                                ->all()
                        )
                        ->placeholder(__('intervention_plan.placeholders.specialist')),
                ]),
            Grid::make()
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->label(__('intervention_plan.labels.status'))
                        ->options(MeetingStatus::options())
                        ->default(MeetingStatus::PLANED)
                        ->required(),
                    DatePicker::make('date')
                        ->label(__('intervention_plan.labels.date'))
                        ->required(),
                    TimePicker::make('time')
                        ->label(__('intervention_plan.labels.time'))
                        ->seconds(false)
                        ->format('H:i')
                        ->displayFormat('H:i'),
                    TextInput::make('duration')
                        ->label(__('intervention_plan.labels.duration'))
                        ->numeric()
                        ->minValue(1)
                        ->maxLength(4),
                ]),
            TextInput::make('topic')
                ->label(__('intervention_plan.labels.topic'))
                ->maxLength(255)
                ->columnSpanFull(),
            Textarea::make('observations')
                ->label(__('intervention_plan.labels.observations'))
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    /**
     * @param  Carbon|\DateTimeInterface|string|null  $state
     */
    private static function formatDate(mixed $state): string
    {
        if ($state === null || $state === '') {
            return '—';
        }

        try {
            return $state instanceof \DateTimeInterface
                ? Carbon::instance($state)->translatedFormat('d.m.Y')
                : Carbon::parse($state)->translatedFormat('d.m.Y');
        } catch (\Throwable) {
            return '—';
        }
    }
}
