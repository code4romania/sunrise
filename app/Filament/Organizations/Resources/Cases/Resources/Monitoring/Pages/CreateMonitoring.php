<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\Monitoring\Pages;

use App\Actions\BackAction;
use App\Enums\ChildAggressorRelationship;
use App\Enums\MaintenanceSources;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use App\Models\Monitoring;
use App\Models\MonitoringChild;
use App\Models\Specialist;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;

class CreateMonitoring extends CreateRecord
{
    use HasWizard;

    protected static string $resource = MonitoringResource::class;

    protected static bool $canCreateAnother = false;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $pendingSpecialistsTeam = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $pendingChildren = [];

    public function mount(): void
    {
        $parent = $this->getParentRecord();
        if ($parent === null || ! $parent instanceof Beneficiary) {
            abort(404);
        }

        if (request('copyLastFile') === '1') {
            $newMonitoring = $this->duplicateLastMonitoring();
            if ($newMonitoring !== null) {
                $this->redirect(MonitoringResource::getUrl('view', [
                    'beneficiary' => $parent,
                    'record' => $newMonitoring,
                ]));

                return;
            }
        }

        $this->authorizeAccess();
        $this->fillForm();
        $this->previousUrl = url()->previous();
    }

    protected function authorizeAccess(): void
    {
        $parent = $this->getParentRecord();
        abort_unless($parent instanceof Beneficiary && CaseResource::canEdit($parent), 403);
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $beneficiary = $this->getParentRecord();
        if (! $beneficiary instanceof Beneficiary) {
            return;
        }

        $lastFile = $beneficiary->monitoring()
            ->with(['children', 'specialistsTeam'])
            ->orderByDesc('id')
            ->first();

        $data = [
            'date' => now()->format('Y-m-d'),
            'number' => null,
            'start_date' => null,
            'end_date' => null,
        ];

        if ($lastFile !== null) {
            $data['admittance_date'] = $lastFile->admittance_date?->format('Y-m-d');
            $data['admittance_disposition'] = $lastFile->admittance_disposition;
            $data['services_in_center'] = $lastFile->services_in_center;
            $data['protection_measures'] = $lastFile->protection_measures;
            $data['health_measures'] = $lastFile->health_measures;
            $data['legal_measures'] = $lastFile->legal_measures;
            $data['psychological_measures'] = $lastFile->psychological_measures;
            $data['aggressor_relationship'] = $lastFile->aggressor_relationship;
            $data['others'] = $lastFile->others;
            $data['progress'] = $lastFile->progress;
            $data['observation'] = $lastFile->observation;

            $data['specialistsTeam'] = $lastFile->specialistsTeam
                ->map(fn (Specialist $s): array => [
                    'role_id' => $s->role_id,
                    'user_id' => $s->user_id,
                    'specialistable_type' => (new Monitoring)->getMorphClass(),
                ])
                ->all();

            $data['children'] = $lastFile->children
                ->map(fn (MonitoringChild $c): array => [
                    'name' => $c->name,
                    'status' => $c->status,
                    'age' => $c->age,
                    'birthdate' => $c->birthdate?->format('d.m.Y'),
                    'aggressor_relationship' => $c->aggressor_relationship?->value,
                    'maintenance_sources' => $c->maintenance_sources?->value,
                    'location' => $c->location,
                    'observations' => $c->observations,
                ])
                ->all();
        } else {
            $data['specialistsTeam'] = $beneficiary->specialistsTeam
                ->filter(fn (Specialist $s): bool => (bool) $s->role_id)
                ->map(fn (Specialist $s): array => [
                    'role_id' => $s->role_id,
                    'user_id' => $s->user_id,
                    'specialistable_type' => (new Monitoring)->getMorphClass(),
                ])
                ->values()
                ->all();

            $data['children'] = $beneficiary->children
                ->map(fn ($c): array => [
                    'name' => $c->name ?? '',
                    'status' => $c->status ?? '',
                    'age' => $c->age !== null ? (string) $c->age : '',
                    'birthdate' => $c->birthdate?->format('d.m.Y'),
                    'aggressor_relationship' => null,
                    'maintenance_sources' => null,
                    'location' => $c->current_address ?? '',
                    'observations' => '',
                ])
                ->all();
        }

        $this->form->fill($data);
        $this->callHook('afterFill');
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.create');
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $parent]) => $parent instanceof Beneficiary ? $parent->getBreadcrumb() : '',
            CaseResource::getUrl('edit_case_monitoring', ['record' => $parent]) => __('monitoring.titles.list'),
            '' => __('monitoring.titles.create'),
        ];
    }

    protected function getHeaderActions(): array
    {
        $parent = $this->getParentRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('edit_case_monitoring', ['record' => $parent])),
        ];
    }

    /**
     * @return array<int, Step>
     */
    public function getSteps(): array
    {
        return [
            Step::make(__('monitoring.headings.details'))
                ->schema($this->getDetailsStepSchema()),
            Step::make(__('monitoring.headings.child_info'))
                ->schema($this->getChildrenStepSchema()),
            Step::make(__('monitoring.headings.general'))
                ->schema($this->getGeneralStepSchema()),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getDetailsStepSchema(): array
    {
        return [
            Grid::make()
                ->maxWidth('3xl')
                ->schema([
                    DatePicker::make('date')
                        ->label(__('monitoring.labels.date')),
                    TextInput::make('number')
                        ->label(__('monitoring.labels.number'))
                        ->placeholder(__('monitoring.placeholders.number'))
                        ->maxLength(100),
                    DatePicker::make('start_date')
                        ->label(__('monitoring.labels.start_date')),
                    DatePicker::make('end_date')
                        ->label(__('monitoring.labels.end_date')),
                    Repeater::make('specialistsTeam')
                        ->minItems(1)
                        ->hiddenLabel()
                        ->columnSpanFull()
                        ->addActionLabel(__('monitoring.actions.add_specialist'))
                        ->columns(3)
                        ->itemLabel(fn (array $state): ?string => isset($state['user_id']) ? User::find($state['user_id'])?->name : null)
                        ->schema([
                            Placeholder::make('nr')
                                ->label(__('nomenclature.labels.nr'))
                                ->content(function (): int {
                                    static $index = 1;

                                    return $index++;
                                })
                                ->hiddenLabel(),
                            Select::make('role_id')
                                ->label(__('monitoring.labels.role'))
                                ->options(
                                    UserRole::query()
                                        ->with('role')
                                        ->get()
                                        ->pluck('role.name', 'role.id')
                                )
                                ->live(),
                            Select::make('user_id')
                                ->label(__('monitoring.labels.specialist_name'))
                                ->options(
                                    fn (Get $get) => UserRole::query()
                                        ->where('role_id', $get('role_id'))
                                        ->with('user')
                                        ->get()
                                        ->pluck('user.full_name', 'user.id')
                                ),
                            Hidden::make('specialistable_type')
                                ->default((new Monitoring)->getMorphClass()),
                        ]),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getChildrenStepSchema(): array
    {
        return [
            Placeholder::make('empty_state_children')
                ->label(__('monitoring.headings.empty_state_children'))
                ->visible(fn (Get $get): bool => empty($get('children'))),
            Repeater::make('children')
                ->hiddenLabel()
                ->maxWidth('3xl')
                ->deletable(false)
                ->addable(false)
                ->schema([
                    TextInput::make('name')
                        ->label(__('monitoring.labels.child_name'))
                        ->columnSpanFull(),
                    Grid::make()
                        ->schema([
                            TextInput::make('status')
                                ->label(__('monitoring.labels.status'))
                                ->maxLength(70),
                            TextInput::make('age')
                                ->label(__('monitoring.labels.age'))
                                ->maxLength(2)
                                ->mask('99'),
                            DatePicker::make('birthdate')
                                ->label(__('monitoring.labels.birthdate')),
                            Select::make('aggressor_relationship')
                                ->label(__('monitoring.labels.aggressor_relationship'))
                                ->placeholder(__('monitoring.placeholders.select_an_answer'))
                                ->options(ChildAggressorRelationship::options()),
                            Select::make('maintenance_sources')
                                ->label(__('monitoring.labels.maintenance_sources'))
                                ->placeholder(__('monitoring.placeholders.select_an_answer'))
                                ->options(MaintenanceSources::options()),
                            TextInput::make('location')
                                ->label(__('monitoring.labels.location'))
                                ->placeholder(__('monitoring.placeholders.location'))
                                ->maxLength(100),
                            Textarea::make('observations')
                                ->label(__('monitoring.labels.observations'))
                                ->placeholder(__('monitoring.placeholders.observations'))
                                ->maxLength(500)
                                ->columnSpanFull(),
                        ]),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getGeneralStepSchema(): array
    {
        $beneficiary = $this->getParentRecord();

        return [
            Group::make()
                ->maxWidth('3xl')
                ->schema([
                    Grid::make()
                        ->schema([
                            DatePicker::make('admittance_date')
                                ->label(__('monitoring.labels.admittance_date'))
                                ->default($beneficiary?->created_at?->format('Y-m-d')),
                            TextInput::make('admittance_disposition')
                                ->label(__('monitoring.labels.admittance_disposition'))
                                ->placeholder(__('monitoring.placeholders.admittance_disposition'))
                                ->maxLength(100),
                        ]),
                    Textarea::make('services_in_center')
                        ->label(__('monitoring.labels.services_in_center'))
                        ->placeholder(__('monitoring.placeholders.services_in_center'))
                        ->maxLength(2500),
                    Checkbox::make('others.option_first')
                        ->label(__('monitoring.labels.option_first')),
                    Checkbox::make('others.option_second')
                        ->label(__('monitoring.labels.option_second'))
                        ->visible(fn (Get $get): bool => (bool) ($get('others.option_first') ?? false))
                        ->disabled(fn (Get $get): bool => ! (bool) ($get('others.option_first') ?? false)),
                    ...$this->getGeneralMonitoringSectionFields(),
                    Placeholder::make('progress_placeholder')
                        ->label(__('monitoring.headings.progress')),
                    Textarea::make('progress')
                        ->label(__('monitoring.labels.progress'))
                        ->placeholder(__('monitoring.placeholders.progress'))
                        ->maxLength(2500),
                    Placeholder::make('observation_placeholder')
                        ->label(__('monitoring.headings.observation')),
                    Textarea::make('observation')
                        ->label(__('monitoring.labels.observation'))
                        ->placeholder(__('monitoring.placeholders.observation'))
                        ->maxLength(2500),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function getGeneralMonitoringSectionFields(): array
    {
        $fields = [
            'protection_measures',
            'health_measures',
            'legal_measures',
            'psychological_measures',
            'aggressor_relationship',
            'others',
        ];
        $out = [];

        foreach ($fields as $field) {
            $out[] = Placeholder::make($field.'_heading')
                ->label(__('monitoring.headings.'.$field));
            $out[] = Textarea::make($field.'.objection')
                ->label(__('monitoring.labels.objection'))
                ->placeholder(__('monitoring.placeholders.add_details'))
                ->maxLength(1500);
            $out[] = Textarea::make($field.'.activity')
                ->label(__('monitoring.labels.activity'))
                ->placeholder(__('monitoring.placeholders.add_details'))
                ->maxLength(1500);
            $out[] = Textarea::make($field.'.conclusion')
                ->label(__('monitoring.labels.conclusion'))
                ->placeholder(__('monitoring.placeholders.add_details'))
                ->maxLength(1500);
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingSpecialistsTeam = $data['specialistsTeam'] ?? [];
        $this->pendingChildren = $data['children'] ?? [];

        unset($data['specialistsTeam'], $data['children']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $monitoring = $this->getRecord();

        foreach ($this->pendingSpecialistsTeam as $item) {
            if (empty($item['role_id']) || empty($item['user_id'])) {
                continue;
            }
            Specialist::create([
                'role_id' => $item['role_id'],
                'user_id' => $item['user_id'],
                'specialistable_type' => (new Monitoring)->getMorphClass(),
                'specialistable_id' => $monitoring->getKey(),
            ]);
        }

        foreach ($this->pendingChildren as $item) {
            $child = [
                'monitoring_id' => $monitoring->getKey(),
                'name' => $item['name'] ?? null,
                'status' => $item['status'] ?? null,
                'age' => $item['age'] ?? null,
                'location' => $item['location'] ?? null,
                'observations' => $item['observations'] ?? null,
            ];
            $birthdateRaw = trim((string) ($item['birthdate'] ?? ''));
            if ($birthdateRaw !== '' && $birthdateRaw !== '-') {
                try {
                    $child['birthdate'] = Carbon::createFromFormat('d.m.Y', $birthdateRaw)->format('Y-m-d');
                } catch (\Throwable) {
                    $child['birthdate'] = null;
                }
            }
            if (isset($item['aggressor_relationship']) && $item['aggressor_relationship'] !== '') {
                $child['aggressor_relationship'] = $item['aggressor_relationship'];
            }
            if (isset($item['maintenance_sources']) && $item['maintenance_sources'] !== '') {
                $child['maintenance_sources'] = $item['maintenance_sources'];
            }
            MonitoringChild::create($child);
        }
    }

    protected function getRedirectUrl(): string
    {
        $parent = $this->getParentRecord();

        return MonitoringResource::getUrl('view', [
            'beneficiary' => $parent,
            'record' => $this->getRecord(),
        ]);
    }

    private function duplicateLastMonitoring(): ?Monitoring
    {
        $beneficiary = $this->getParentRecord();
        if (! $beneficiary instanceof Beneficiary) {
            return null;
        }

        $last = $beneficiary->monitoring()
            ->with(['children', 'specialistsTeam'])
            ->orderByDesc('id')
            ->first();

        if ($last === null) {
            return null;
        }

        $newMonitoring = $beneficiary->monitoring()->create([
            'date' => now(),
            'number' => $last->number,
            'start_date' => $last->start_date,
            'end_date' => $last->end_date,
            'admittance_date' => $last->admittance_date,
            'admittance_disposition' => $last->admittance_disposition,
            'services_in_center' => $last->services_in_center,
            'protection_measures' => $last->protection_measures,
            'health_measures' => $last->health_measures,
            'legal_measures' => $last->legal_measures,
            'psychological_measures' => $last->psychological_measures,
            'aggressor_relationship' => $last->aggressor_relationship,
            'others' => $last->others,
            'progress' => $last->progress,
            'observation' => $last->observation,
        ]);

        foreach ($last->specialistsTeam as $s) {
            Specialist::create([
                'role_id' => $s->role_id,
                'user_id' => $s->user_id,
                'specialistable_type' => (new Monitoring)->getMorphClass(),
                'specialistable_id' => $newMonitoring->getKey(),
            ]);
        }

        foreach ($last->children as $c) {
            MonitoringChild::create([
                'monitoring_id' => $newMonitoring->getKey(),
                'name' => $c->name,
                'status' => $c->status,
                'age' => $c->age,
                'birthdate' => $c->birthdate,
                'aggressor_relationship' => $c->aggressor_relationship?->value,
                'maintenance_sources' => $c->maintenance_sources?->value,
                'location' => $c->location,
                'observations' => $c->observations,
            ]);
        }

        return $newMonitoring;
    }
}
