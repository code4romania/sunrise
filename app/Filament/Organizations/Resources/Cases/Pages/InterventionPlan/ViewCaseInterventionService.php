<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Enums\MeetingStatus;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\InterventionService;
use App\Models\OrganizationServiceIntervention;
use App\Models\Specialist;
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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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
                        Tab::make(__('intervention_plan.headings.intervention_meetings'))
                            ->icon(Heroicon::OutlinedCalendarDays)
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextEntry::make('organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition.name')
                                            ->label(__('intervention_plan.labels.service'))
                                            ->placeholder('—'),
                                        TextEntry::make('specialist.name_role')
                                            ->label(__('intervention_plan.labels.specialist'))
                                            ->placeholder('—'),
                                        TextEntry::make('institution')
                                            ->label(__('intervention_plan.labels.responsible_institution'))
                                            ->placeholder('—'),
                                        TextEntry::make('start_date_interval')
                                            ->label(__('intervention_plan.labels.start_date_interval'))
                                            ->formatStateUsing(fn ($state) => self::formatDate($state))
                                            ->placeholder('—'),
                                        TextEntry::make('end_date_interval')
                                            ->label(__('intervention_plan.labels.end_date_interval'))
                                            ->formatStateUsing(fn ($state) => self::formatDate($state))
                                            ->placeholder('—'),
                                        TextEntry::make('interventions_count')
                                            ->label(__('intervention_plan.labels.interventions_count'))
                                            ->state((string) $service->beneficiaryInterventions()->count())
                                            ->placeholder('0'),
                                        TextEntry::make('meetings_count')
                                            ->label(__('intervention_plan.labels.meetings_count'))
                                            ->state((string) $service->meetings()->count())
                                            ->placeholder('0'),
                                    ])
                                    ->columns(2)
                                    ->headerActions([
                                        Action::make('add_meeting')
                                            ->label(__('intervention_plan.actions.add_meeting'))
                                            ->icon(Heroicon::OutlinedPlus)
                                            ->color('primary')
                                            ->modalHeading(__('intervention_plan.actions.add_meeting'))
                                            ->schema($this->getAddMeetingFormSchema())
                                            ->action(function (array $data): void {
                                                $beneficiaryIntervention = $this->interventionService->beneficiaryInterventions()->create([
                                                    'organization_service_intervention_id' => $data['organization_service_intervention_id'],
                                                    'specialist_id' => $data['specialist_id'] ?? null,
                                                    'start_date_interval' => $data['date'] ?? null,
                                                    'end_date_interval' => $data['date'] ?? null,
                                                ]);
                                                $status = $data['status'] instanceof MeetingStatus
                                                    ? $data['status']
                                                    : MeetingStatus::tryFrom($data['status']);
                                                $beneficiaryIntervention->meetings()->create([
                                                    'specialist_id' => $data['specialist_id'] ?? null,
                                                    'status' => $status ?? MeetingStatus::PLANED,
                                                    'date' => $data['date'] ?? null,
                                                    'time' => $data['time'] ?? null,
                                                    'duration' => isset($data['duration']) ? (int) $data['duration'] : null,
                                                    'topic' => $data['topic'] ?? null,
                                                    'observations' => $data['observations'] ?? null,
                                                ]);
                                                Notification::make()
                                                    ->success()
                                                    ->title(__('filament-actions::create.single.notifications.created.title'))
                                                    ->send();
                                                $this->redirect(CaseResource::getUrl('view_intervention_service', [
                                                    'record' => $this->record,
                                                    'interventionService' => $this->interventionService,
                                                ]));
                                            }),
                                    ]),
                                Section::make(__('intervention_plan.headings.meetings_list'))
                                    ->schema([
                                        RepeatableEntry::make('meetings')
                                            ->hiddenLabel()
                                            ->state(fn () => $service->meetings()->with('specialist.user', 'specialist.roleForDisplay')->orderByDesc('date')->orderByDesc('time')->get())
                                            ->placeholder(__('intervention_plan.labels.empty_meetings_list'))
                                            ->table([
                                                TableColumn::make(__('intervention_plan.labels.status')),
                                                TableColumn::make(__('intervention_plan.labels.date')),
                                                TableColumn::make(__('intervention_plan.labels.time')),
                                                TableColumn::make(__('intervention_plan.labels.duration')),
                                                TableColumn::make(__('intervention_plan.labels.topic')),
                                                TableColumn::make(__('intervention_plan.labels.observations')),
                                            ])
                                            ->schema([
                                                TextEntry::make('status'),
                                                TextEntry::make('date')
                                                    ->formatStateUsing(fn ($state) => self::formatDate($state)),
                                                TextEntry::make('time')
                                                    ->formatStateUsing(fn ($state) => $state instanceof \DateTimeInterface ? Carbon::instance($state)->format('H:i') : ($state ?: '—')),
                                                TextEntry::make('duration')
                                                    ->formatStateUsing(fn ($state) => $state !== null ? $state.' min' : '—'),
                                                TextEntry::make('topic')
                                                    ->placeholder('—'),
                                                TextEntry::make('observations')
                                                    ->placeholder('—')
                                                    ->limit(50),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ]),
                        Tab::make(__('intervention_plan.headings.intervention_indicators'))
                            ->icon(Heroicon::OutlinedChartBar)
                            ->schema([
                                Section::make(__('intervention_plan.headings.intervention_indicators'))
                                    ->schema([]),
                            ]),
                        Tab::make(__('intervention_plan.headings.unfolded'))
                            ->icon(Heroicon::OutlinedTableCells)
                            ->schema([
                                Section::make(__('intervention_plan.headings.unfolded_table'))
                                    ->schema([
                                        RepeatableEntry::make('unfolded_meetings')
                                            ->hiddenLabel()
                                            ->state(function () use ($service) {
                                                $meetings = $service->meetings()
                                                    ->with(['specialist.user', 'specialist.roleForDisplay'])
                                                    ->orderByDesc('date')
                                                    ->orderByDesc('time')
                                                    ->get();

                                                return $meetings->map(function ($meeting, $index) {
                                                    return [
                                                        'meet_number' => $index + 1,
                                                        'status' => $meeting->status,
                                                        'date' => $meeting->date,
                                                        'time' => $meeting->time,
                                                        'duration' => $meeting->duration,
                                                        'specialist_name' => $meeting->specialist?->name_role ?? '—',
                                                        'topic' => $meeting->topic,
                                                        'observations' => $meeting->observations,
                                                    ];
                                                })->values()->all();
                                            })
                                            ->placeholder(__('intervention_plan.labels.empty_meetings_list'))
                                            ->table([
                                                TableColumn::make(__('intervention_plan.labels.meet_number')),
                                                TableColumn::make(__('intervention_plan.labels.status')),
                                                TableColumn::make(__('intervention_plan.labels.date')),
                                                TableColumn::make(__('intervention_plan.labels.time')),
                                                TableColumn::make(__('intervention_plan.labels.duration')),
                                                TableColumn::make(__('intervention_plan.labels.specialist')),
                                                TableColumn::make(__('intervention_plan.labels.topic')),
                                                TableColumn::make(__('intervention_plan.labels.observations')),
                                            ])
                                            ->schema([
                                                TextEntry::make('meet_number'),
                                                TextEntry::make('status'),
                                                TextEntry::make('date')
                                                    ->formatStateUsing(fn ($state) => $state ? self::formatDate($state) : '—'),
                                                TextEntry::make('time')
                                                    ->formatStateUsing(fn ($state) => $state instanceof \DateTimeInterface ? Carbon::instance($state)->format('H:i') : ($state ?: '—')),
                                                TextEntry::make('duration')
                                                    ->formatStateUsing(fn ($state) => $state !== null ? $state.' min' : '—'),
                                                TextEntry::make('specialist_name')
                                                    ->placeholder('—'),
                                                TextEntry::make('topic')
                                                    ->placeholder('—'),
                                                TextEntry::make('observations')
                                                    ->placeholder('—')
                                                    ->limit(80),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ]);
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
