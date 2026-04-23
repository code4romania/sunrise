<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Enums\CounselingSheet;
use App\Filament\Organizations\Concerns\InteractsWithBeneficiaryDetailsPanel;
use App\Filament\Organizations\Concerns\InteractsWithInterventionMeetingForm;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionMeeting;
use App\Models\InterventionService;
use App\Models\OrganizationServiceIntervention;
use App\Models\ServiceCounselingSheet;
use App\Models\Specialist;
use App\Schemas\CounselingSheetFormSchemas;
use App\Schemas\CounselingSheetInfolistSchemas;
use App\Services\CaseExports\CaseExportManager;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewCaseInterventionService extends ViewRecord
{
    use InteractsWithBeneficiaryDetailsPanel;
    use InteractsWithInterventionMeetingForm;

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
            $this->getEditMeetingAction(),
            $this->getViewMeetingObservationsAction(),
            Action::make('download_psychological_counseling_sheet')
                ->label(__('intervention_plan.actions.download_psychological_counseling_sheet'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->outlined()
                ->visible(fn (): bool => $this->getCounselingSheetType() === CounselingSheet::PSYCHOLOGICAL_ASSISTANCE)
                ->action(function (): StreamedResponse {
                    if (! $this->interventionService instanceof InterventionService) {
                        abort(404);
                    }

                    return app(CaseExportManager::class)->downloadPsychologicalCounselingSheetPdf($this->interventionService);
                }),
            Action::make('download_legal_counseling_sheet')
                ->label(__('intervention_plan.actions.download_legal_counseling_sheet'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->outlined()
                ->visible(fn (): bool => $this->getCounselingSheetType() === CounselingSheet::LEGAL_ASSISTANCE)
                ->action(function (): StreamedResponse {
                    if (! $this->interventionService instanceof InterventionService) {
                        abort(404);
                    }

                    return app(CaseExportManager::class)->downloadLegalCounselingSheetPdf($this->interventionService);
                }),
            Action::make('download_social_counseling_sheet')
                ->label(__('intervention_plan.actions.download_social_counseling_sheet'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->outlined()
                ->visible(fn (): bool => $this->getCounselingSheetType() === CounselingSheet::SOCIAL_ASSISTANCE)
                ->action(function (): StreamedResponse {
                    if (! $this->interventionService instanceof InterventionService) {
                        abort(404);
                    }

                    return app(CaseExportManager::class)->downloadSocialCounselingSheetPdf($this->interventionService);
                }),
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

    /**
     * @return array<Action | ActionGroup>
     */
    public function getCachedHeaderActions(): array
    {
        return array_values(array_filter(
            parent::getCachedHeaderActions(),
            function (mixed $action): bool {
                if (! $action instanceof Action) {
                    return true;
                }

                return ! in_array($action->getName(), ['edit_meeting', 'view_meeting_observations'], true);
            }
        ));
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
                                                $rawIds = $data['organization_service_intervention_ids'] ?? [];
                                                $ids = \is_array($rawIds)
                                                    ? array_values(array_filter(
                                                        $rawIds,
                                                        static fn (mixed $id): bool => $id !== null && $id !== ''
                                                    ))
                                                    : [];

                                                if ($ids === []) {
                                                    return;
                                                }

                                                $payload = [
                                                    'specialist_id' => $data['specialist_id'] ?? null,
                                                    'start_date_interval' => $data['start_date_interval'] ?? null,
                                                    'end_date_interval' => $data['end_date_interval'] ?? null,
                                                    'objections' => $data['objections'] ?? null,
                                                    'expected_results' => $data['expected_results'] ?? null,
                                                    'procedure' => $data['procedure'] ?? null,
                                                    'indicators' => $data['indicators'] ?? null,
                                                    'achievement_degree' => $data['achievement_degree'] ?? null,
                                                ];

                                                DB::transaction(function () use ($ids, $payload): void {
                                                    foreach ($ids as $osiId) {
                                                        $this->interventionService->beneficiaryInterventions()->create([
                                                            'organization_service_intervention_id' => (int) $osiId,
                                                            ...$payload,
                                                        ]);
                                                    }
                                                });

                                                Notification::make()
                                                    ->success()
                                                    ->title(trans_choice('intervention_plan.notifications.interventions_added', \count($ids)))
                                                    ->send();

                                                $this->redirect(CaseResource::getUrl('view_intervention_service', [
                                                    'record' => $this->record,
                                                    'interventionService' => $this->interventionService,
                                                ]));
                                            }),
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
                        Tab::make(__('intervention_plan.headings.service_meetings'))
                            ->schema([
                                Section::make(__('intervention_plan.headings.service_meetings'))
                                    ->schema([
                                        View::make('filament.organizations.components.service-aggregated-meetings'),
                                    ])
                                    ->headerActions([
                                        Action::make('add_meeting')
                                            ->label(__('intervention_plan.actions.add_meeting'))
                                            ->icon(Heroicon::OutlinedPlus)
                                            ->color('primary')
                                            ->modalHeading(__('intervention_plan.actions.add_meeting'))
                                            ->modalSubmitActionLabel(__('general.action.save'))
                                            ->modalCancelActionLabel(__('general.action.cancel'))
                                            ->visible(fn (): bool => $this->interventionService !== null
                                                && $this->interventionService->beneficiaryInterventions()->exists())
                                            ->form($this->getAddMeetingOnServiceFormSchema())
                                            ->fillForm(fn (): array => array_merge(
                                                ['beneficiary_intervention_id' => null],
                                                $this->interventionMeetingFormDefaultState(null)
                                            ))
                                            ->action(function (array $data): void {
                                                $interventionId = $data['beneficiary_intervention_id'] ?? null;
                                                unset($data['beneficiary_intervention_id']);
                                                if (blank($interventionId) || ! $this->interventionService instanceof InterventionService) {
                                                    return;
                                                }

                                                $beneficiaryIntervention = BeneficiaryIntervention::query()
                                                    ->whereKey($interventionId)
                                                    ->where('intervention_service_id', $this->interventionService->getKey())
                                                    ->first();

                                                if (! $beneficiaryIntervention instanceof BeneficiaryIntervention) {
                                                    return;
                                                }

                                                $beneficiaryIntervention->meetings()->create($data);
                                                Notification::make()
                                                    ->success()
                                                    ->title(__('filament-actions::create.single.notifications.created.title'))
                                                    ->send();
                                            }),
                                    ]),
                            ]),

                        //                        Tab::make(__('intervention_plan.headings.results_obtained'))
                        //                            ->schema([
                        //                                Section::make(__('intervention_plan.headings.results_obtained'))
                        //                                    ->schema([
                        //                                        RepeatableEntry::make('beneficiaryInterventionsResults')
                        //                                            ->hiddenLabel()
                        //                                            ->state(fn () => $service->beneficiaryInterventions()
                        //                                                ->with(['organizationServiceIntervention.serviceInterventionWithoutStatusCondition'])
                        //                                                ->orderByDesc('id')
                        //                                                ->get())
                        //                                            ->placeholder(__('intervention_plan.headings.empty_state_result_table'))
                        //                                            ->table([
                        //                                                TableColumn::make(__('intervention_plan.labels.service_type')),
                        //                                                TableColumn::make(__('intervention_plan.labels.expected_results')),
                        //                                                TableColumn::make(__('intervention_plan.labels.achievement_degree')),
                        //                                                TableColumn::make(__('intervention_plan.labels.procedure')),
                        //                                            ])
                        //                                            ->schema([
                        //                                                TextEntry::make('organizationServiceIntervention.serviceInterventionWithoutStatusCondition.name')
                        //                                                    ->placeholder('—'),
                        //                                                TextEntry::make('expected_results')
                        //                                                    ->placeholder('—')
                        //                                                    ->limit(50),
                        //                                                TextEntry::make('achievement_degree')
                        //                                                    ->placeholder('—'),
                        //                                                TextEntry::make('procedure')
                        //                                                    ->placeholder('—')
                        //                                                    ->limit(30),
                        //                                            ]),
                        //                                    ]),
                        //                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getAddInterventionFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    CheckboxList::make('organization_service_intervention_ids')
                        ->label(__('intervention_plan.labels.intervention_type'))
                        ->options(fn (): array => $this->getAddInterventionOrganizationServiceInterventionOptions())
                        ->required()
                        ->columns(2)
                        ->gridDirection('row')
                        ->helperText(__('intervention_plan.helpers.add_interventions_multiple')),
                    Select::make('specialist_id')
                        ->label(__('intervention_plan.labels.responsible_person'))
                        ->options(fn (): array => $this->getCaseTeamSpecialistOptionsForBeneficiary())
                        ->placeholder(__('intervention_plan.placeholders.specialist'))
                        ->helperText(fn (): ?string => \count($this->getCaseTeamSpecialistOptionsForBeneficiary()) === 0
                            ? __('intervention_plan.helpers.empty_case_team_for_responsible')
                            : null),
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

    /**
     * @return array<int|string, string>
     */
    protected function getAddInterventionOrganizationServiceInterventionOptions(): array
    {
        $service = $this->interventionService;
        if ($service === null) {
            return [];
        }

        return OrganizationServiceIntervention::with('serviceInterventionWithoutStatusCondition')
            ->where('organization_service_id', $service->organization_service_id)
            ->active()
            ->get()
            ->filter(fn (OrganizationServiceIntervention $osi) => $osi->serviceInterventionWithoutStatusCondition)
            ->mapWithKeys(fn (OrganizationServiceIntervention $osi) => [
                $osi->id => $osi->serviceInterventionWithoutStatusCondition->name,
            ])
            ->all();
    }

    /**
     * @return array<int|string, string>
     */
    protected function getCaseTeamSpecialistOptionsForBeneficiary(): array
    {
        $beneficiary = $this->record;
        if (! $beneficiary instanceof Beneficiary) {
            return [];
        }

        return $beneficiary->specialistsTeam()
            ->with(['user:id,first_name,last_name', 'roleForDisplay:id,name'])
            ->get()
            ->mapWithKeys(fn (Specialist $s) => [$s->id => $s->name_role])
            ->all();
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
    protected function getMeetingFormSchema(): array
    {
        $beneficiary = $this->record instanceof Beneficiary ? $this->record : null;

        return $this->interventionMeetingFormSchema($beneficiary);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getAddMeetingOnServiceFormSchema(): array
    {
        $beneficiary = $this->record instanceof Beneficiary ? $this->record : null;
        if ($beneficiary === null) {
            return [];
        }

        return array_merge(
            [
                Select::make('beneficiary_intervention_id')
                    ->label(__('intervention_plan.labels.intervention'))
                    ->options($this->getBeneficiaryInterventionSelectOptions())
                    ->required(),
            ],
            $this->interventionMeetingFormSchema($beneficiary)
        );
    }

    /**
     * @return array<int|string, string>
     */
    protected function getBeneficiaryInterventionSelectOptions(): array
    {
        $service = $this->interventionService;
        if ($service === null) {
            return [];
        }

        return $service->beneficiaryInterventions()
            ->with(['organizationServiceIntervention.serviceInterventionWithoutStatusCondition'])
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(fn (BeneficiaryIntervention $bi): array => [
                $bi->getKey() => $bi->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name ?? '—',
            ])
            ->all();
    }

    protected function getEditMeetingAction(): Action
    {
        return Action::make('edit_meeting')
            ->modalSubmitActionLabel(__('general.action.save'))
            ->modalCancelActionLabel(__('general.action.cancel'))
            ->modalHeading(fn (array $arguments): string => __('intervention_plan.headings.meeting_repeater', [
                'number' => $this->getMeetingNumberForId((int) ($arguments['meeting'] ?? 0)),
            ]))
            ->form($this->getMeetingFormSchema())
            ->fillForm(function (array $arguments): array {
                $meeting = InterventionMeeting::find($arguments['meeting'] ?? null);

                return $meeting && $this->meetingBelongsToCurrentService($meeting)
                    ? $meeting->only([
                        'status', 'date', 'time', 'duration', 'specialist_id', 'topic', 'observations',
                    ])
                    : [];
            })
            ->extraModalFooterActions([
                DeleteAction::make('delete_meeting')
                    ->label(__('intervention_plan.actions.delete_meeting'))
                    ->modalHeading(__('intervention_plan.actions.delete_meeting'))
                    ->action(function (array $mountedActions): void {
                        $arguments = $mountedActions[0]->getArguments();
                        $meeting = InterventionMeeting::find($arguments['meeting'] ?? null);
                        if ($meeting && $this->meetingBelongsToCurrentService($meeting)) {
                            $meeting->delete();
                            Notification::make()
                                ->success()
                                ->title(__('filament-actions::delete.single.notifications.deleted.title'))
                                ->send();
                        }
                    }),
            ])
            ->action(function (array $arguments, array $data): void {
                $meeting = InterventionMeeting::find($arguments['meeting'] ?? null);
                if ($meeting && $this->meetingBelongsToCurrentService($meeting)) {
                    $meeting->update($data);
                    Notification::make()
                        ->success()
                        ->title(__('filament-actions::edit.single.notifications.saved.title'))
                        ->send();
                }
            });
    }

    protected function getViewMeetingObservationsAction(): Action
    {
        return Action::make('view_meeting_observations')
            ->modalHeading(__('general.action.view_observations'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('general.action.close'))
            ->modalContent(function (): Htmlable {
                $action = $this->getMountedAction();
                $meetingId = $action?->getArguments()['meeting'] ?? null;
                $meeting = $meetingId
                    ? InterventionMeeting::with('specialist.user', 'specialist.roleForDisplay')
                        ->whereKey($meetingId)
                        ->whereHas('beneficiaryIntervention', function ($query): void {
                            $query->where('intervention_service_id', $this->interventionService?->getKey());
                        })
                        ->first()
                    : null;

                return new HtmlString(
                    $meeting
                        ? view('filament.organizations.components.meeting-observations-modal-content', ['meeting' => $meeting])->render()
                        : '<p class="text-sm text-gray-500 dark:text-gray-400">—</p>'
                );
            });
    }

    protected function getMeetingNumberForId(int $meetingId): int
    {
        $meetingsTable = (new InterventionMeeting)->getTable();
        $meetings = $this->interventionService?->meetings()
            ->orderByDesc("{$meetingsTable}.id")
            ->pluck("{$meetingsTable}.id")
            ->values() ?? collect();

        $index = $meetings->search($meetingId);

        return $index !== false ? $meetings->count() - $index : 1;
    }

    protected function meetingBelongsToCurrentService(?InterventionMeeting $meeting): bool
    {
        if ($meeting === null || ! $this->interventionService instanceof InterventionService) {
            return false;
        }

        if ($meeting->beneficiary_intervention_id === null) {
            return false;
        }

        return BeneficiaryIntervention::query()
            ->whereKey($meeting->beneficiary_intervention_id)
            ->where('intervention_service_id', $this->interventionService->getKey())
            ->exists();
    }
}
