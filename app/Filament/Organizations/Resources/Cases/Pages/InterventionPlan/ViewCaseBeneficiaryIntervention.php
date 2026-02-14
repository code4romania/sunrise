<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Enums\MeetingStatus;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionMeeting;
use App\Models\InterventionService;
use App\Models\Specialist;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ViewCaseBeneficiaryIntervention extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public ?BeneficiaryIntervention $beneficiaryIntervention = null;

    public function mount(int|string $record, int|string|Model|null $interventionService = null, int|string|Model|null $beneficiaryIntervention = null): void
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
        $beneficiaryInterventionId = $beneficiaryIntervention instanceof Model
            ? $beneficiaryIntervention->getKey()
            : ($beneficiaryIntervention ?? request()->route('beneficiaryIntervention'));

        if (blank($interventionServiceId) || blank($beneficiaryInterventionId)) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->record]));

            return;
        }

        $service = InterventionService::query()
            ->where('intervention_plan_id', $plan->id)
            ->where((new InterventionService)->getKeyName(), $interventionServiceId)
            ->first();

        if (! $service) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->record]));

            return;
        }

        $this->beneficiaryIntervention = BeneficiaryIntervention::query()
            ->where('intervention_service_id', $service->id)
            ->where((new BeneficiaryIntervention)->getKeyName(), $beneficiaryInterventionId)
            ->with(['organizationServiceIntervention.serviceInterventionWithoutStatusCondition', 'specialist.user', 'specialist.roleForDisplay'])
            ->first();

        if (! $this->beneficiaryIntervention) {
            $this->redirect(CaseResource::getUrl('view_intervention_service', [
                'record' => $this->record,
                'interventionService' => $service,
            ]));

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
        return $this->beneficiaryIntervention?->organizationServiceIntervention?->serviceInterventionWithoutStatusCondition?->name
            ?? __('intervention_plan.headings.interventions');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();
        $service = $this->beneficiaryIntervention?->interventionService;

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            CaseResource::getUrl('view_intervention_service', ['record' => $record, 'interventionService' => $service]) => $service?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? __('intervention_plan.headings.services'),
            '' => $this->getTitle(),
        ];
    }

    protected function getHeaderActions(): array
    {
        $service = $this->beneficiaryIntervention?->interventionService;

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_intervention_service', [
                    'record' => $this->record,
                    'interventionService' => $service,
                ])),
            $this->getEditMeetingAction(),
            $this->getViewMeetingObservationsAction(),
        ];
    }

    protected function getEditMeetingAction(): Action
    {
        return Action::make('edit_meeting')
            ->hidden()
            ->modalSubmitActionLabel(__('general.action.save'))
            ->modalCancelActionLabel(__('general.action.cancel'))
            ->modalHeading(fn (array $arguments): string => __('intervention_plan.headings.meeting_repeater', [
                'number' => $this->getMeetingNumberForId($arguments['meeting'] ?? 0),
            ]))
            ->form($this->getMeetingFormSchema())
            ->fillForm(function (array $arguments): array {
                $meeting = InterventionMeeting::find($arguments['meeting'] ?? null);

                return $meeting ? $meeting->only([
                    'status', 'date', 'time', 'duration', 'specialist_id', 'topic', 'observations',
                ]) : [];
            })
            ->extraModalFooterActions([
                DeleteAction::make('delete_meeting')
                    ->label(__('intervention_plan.actions.delete_meeting'))
                    ->modalHeading(__('intervention_plan.actions.delete_meeting'))
                    ->action(function (array $mountedActions): void {
                        $arguments = $mountedActions[0]->getArguments();
                        $meeting = InterventionMeeting::find($arguments['meeting'] ?? null);
                        if ($meeting && $meeting->beneficiary_intervention_id === $this->beneficiaryIntervention?->getKey()) {
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
                if ($meeting && $meeting->beneficiary_intervention_id === $this->beneficiaryIntervention?->getKey()) {
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
            ->hidden()
            ->modalHeading(__('general.action.view_observations'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('general.action.close'))
            ->modalContent(function (): \Illuminate\Contracts\Support\Htmlable {
                $action = $this->getMountedAction();
                $meetingId = $action?->getArguments()['meeting'] ?? null;
                $meeting = $meetingId ? InterventionMeeting::with('specialist.user', 'specialist.roleForDisplay')
                    ->where('beneficiary_intervention_id', $this->beneficiaryIntervention?->getKey())
                    ->find($meetingId) : null;

                return new \Illuminate\Support\HtmlString(
                    $meeting
                        ? view('filament.organizations.components.meeting-observations-modal-content', ['meeting' => $meeting])->render()
                        : '<p class="text-sm text-gray-500 dark:text-gray-400">—</p>'
                );
            });
    }

    public function downloadMeetingsTable(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $meetings = $this->beneficiaryIntervention?->meetings()
            ->with(['specialist.user', 'specialist.roleForDisplay'])
            ->orderByDesc('id')
            ->get() ?? collect();

        $filename = 'evidenta-sedinte-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(
            function () use ($meetings): void {
                $out = fopen('php://output', 'w');
                if ($out === false) {
                    return;
                }
                fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($out, [
                    __('intervention_plan.labels.meet_number'),
                    __('intervention_plan.labels.status'),
                    __('intervention_plan.labels.date'),
                    __('intervention_plan.labels.time'),
                    __('intervention_plan.labels.duration'),
                    __('intervention_plan.labels.specialist'),
                ]);
                $number = $meetings->count();
                foreach ($meetings as $meeting) {
                    fputcsv($out, [
                        $number,
                        $meeting->status?->getLabel() ?? '',
                        $meeting->date?->translatedFormat('d/m/Y') ?? '',
                        $meeting->time ? \Carbon\Carbon::parse($meeting->time)->format('H:i') : '',
                        $meeting->duration !== null ? $meeting->duration.' min' : '',
                        $meeting->specialist?->name_role ?? '',
                    ]);
                    $number--;
                }
                fclose($out);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }

    protected function getMeetingNumberForId(int $meetingId): int
    {
        $meetings = $this->beneficiaryIntervention?->meetings()->orderByDesc('id')->pluck('id')->values() ?? collect();

        $index = $meetings->search($meetingId);

        return $index !== false ? $meetings->count() - $index : 1;
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getMeetingFormSchema(): array
    {
        $beneficiary = $this->record instanceof Beneficiary ? $this->record : null;

        return [
            Grid::make()
                ->columns(3)
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
                ]),
            Grid::make()
                ->columns(3)
                ->schema([
                    TextInput::make('duration')
                        ->label(__('intervention_plan.labels.duration'))
                        ->numeric()
                        ->minValue(1)
                        ->maxLength(4)
                        ->suffix('min'),
                    Select::make('specialist_id')
                        ->label(__('intervention_plan.labels.responsible_specialist'))
                        ->options(
                            $beneficiary
                                ? $beneficiary->specialistsTeam()
                                    ->with(['user:id,first_name,last_name', 'roleForDisplay:id,name'])
                                    ->get()
                                    ->mapWithKeys(fn (Specialist $s) => [$s->id => $s->name_role])
                                    ->all()
                                : []
                        )
                        ->placeholder(__('intervention_plan.placeholders.specialist')),
                    Textarea::make('topic')
                        ->label(__('intervention_plan.labels.topics_covered'))
                        ->rows(2)
                        ->placeholder(__('intervention_plan.placeholders.add_details')),
                ]),
            RichEditor::make('observations')
                ->label(__('intervention_plan.labels.observations'))
                ->placeholder(__('intervention_plan.placeholders.service_details'))
                ->columnSpanFull(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        $intervention = $this->beneficiaryIntervention;

        return $schema
            ->record($intervention)
            ->components([
                Tabs::make()
                    ->persistTabInQueryString('beneficiary-intervention-tab')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('intervention_plan.headings.intervention_indicators'))
                            ->schema([
                                Section::make(__('intervention_plan.headings.intervention_indicators'))
                                    ->schema([
                                        TextEntry::make('objections')
                                            ->label(__('intervention_plan.labels.specific_objectives'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('expected_results')
                                            ->label(__('intervention_plan.labels.expected_results'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('procedure')
                                            ->label(__('intervention_plan.labels.procedure'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('indicators')
                                            ->label(__('intervention_plan.labels.indicators'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('achievement_degree')
                                            ->label(__('intervention_plan.labels.achievement_degree'))
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->headerActions([
                                        Action::make('edit_indicators')
                                            ->label(__('general.action.edit'))
                                            ->icon(Heroicon::OutlinedPencilSquare)
                                            ->modalHeading(__('intervention_plan.headings.intervention_indicators'))
                                            ->fillForm(fn (): array => $this->beneficiaryIntervention
                                                ? $this->beneficiaryIntervention->only([
                                                    'objections',
                                                    'expected_results',
                                                    'procedure',
                                                    'indicators',
                                                    'achievement_degree',
                                                ])
                                                : [])
                                            ->form([
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
                                            ->action(function (array $data): void {
                                                if ($this->beneficiaryIntervention) {
                                                    $this->beneficiaryIntervention->update($data);
                                                    Notification::make()
                                                        ->success()
                                                        ->title(__('filament-actions::edit.single.notifications.saved.title'))
                                                        ->send();
                                                }
                                            }),
                                    ]),
                            ]),
                        Tab::make(__('intervention_plan.headings.intervention_meetings'))
                            ->schema([
                                Section::make(__('intervention_plan.headings.intervention_meetings'))
                                    ->schema([
                                        View::make('filament.organizations.components.meeting-cards'),
                                    ])
                                    ->headerActions([
                                        Action::make('add_meeting')
                                            ->label(__('intervention_plan.actions.add_meeting'))
                                            ->icon(Heroicon::OutlinedPlus)
                                            ->color('primary')
                                            ->modalHeading(__('intervention_plan.actions.add_meeting'))
                                            ->modalSubmitActionLabel(__('general.action.save'))
                                            ->modalCancelActionLabel(__('general.action.cancel'))
                                            ->form($this->getMeetingFormSchema())
                                            ->fillForm(fn (): array => [
                                                'status' => MeetingStatus::PLANED,
                                                'date' => now()->format('Y-m-d'),
                                                'time' => null,
                                                'duration' => 60,
                                                'specialist_id' => $this->beneficiaryIntervention?->specialist_id,
                                                'topic' => null,
                                                'observations' => null,
                                            ])
                                            ->action(function (array $data): void {
                                                if ($this->beneficiaryIntervention) {
                                                    $this->beneficiaryIntervention->meetings()->create($data);
                                                    Notification::make()
                                                        ->success()
                                                        ->title(__('filament-actions::create.single.notifications.created.title'))
                                                        ->send();
                                                }
                                            }),
                                    ]),
                            ]),
                        Tab::make(__('intervention_plan.headings.unfolded'))
                            ->schema([
                                View::make('filament.organizations.components.unfolded-meetings-table'),
                            ]),
                    ]),
            ]);
    }
}
