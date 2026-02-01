<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;

use App\Actions\BackAction;
use App\Concerns\HasGroupPages;
use App\Concerns\HasParentResource;
use App\Enums\MeetingStatus;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Filament\Organizations\Resources\InterventionPlanResource;
use App\Filament\Organizations\Resources\InterventionServiceResource;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Select;
use App\Infolists\Components\Actions\CreateAction;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\SectionHeader;
use App\Models\InterventionMeeting;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class ViewMeetings extends ViewRecord
{
    use HasGroupPages;
    use HasParentResource;

    protected static string $resource = BeneficiaryInterventionResource::class;

    protected string $view = 'filament.organizations.pages.view-beneficiary-interventions';

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionService->interventionPlan)
            ->getInterventionBreadcrumb($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->organizationServiceIntervention->serviceInterventionWithoutStatusCondition->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(
                    InterventionPlanResource::getUrl('view_intervention_service', [
                        'parent' => $this->parent->interventionPlan,
                        'record' => $this->parent,
                    ])
                ),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('status')
                ->label(__('intervention_plan.labels.status'))
                ->options(MeetingStatus::options()),

            DatePicker::make('date')
                ->label(__('intervention_plan.labels.date')),

            TimePicker::make('time')
                ->label(__('intervention_plan.labels.time'))
                ->seconds(false)
                ->format('H:i')
                ->displayFormat('H:i'),

            TextInput::make('duration')
                ->label(__('intervention_plan.labels.duration'))
                ->maxLength(3)
                ->numeric(),

            Select::make('specialist_id')
                ->label(__('intervention_plan.labels.responsible_specialist'))
                ->options(
                    fn () => $this->getRecord()
                        ->beneficiary
                        ->specialistsTeam
                        ->loadMissing([
                            'user:id,first_name,last_name',
                            'role:id,name',
                        ])
                        ->pluck('name_role', 'id')
                ),

            RichEditor::make('observations')
                ->label(__('intervention_plan.labels.observations'))
                ->columnSpanFull(),

            Hidden::make('beneficiary_intervention_id')
                ->default($this->record->id),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        $this->getRecord()->loadMissing([
            'meetings.specialist.user:id,first_name,last_name',
            'meetings.specialist.role:id,name',
        ]);

        return $schema->components([
            Section::make()
                ->key('meetings')
                ->headerActions([
                    CreateAction::make('create_meeting')
                        ->label(__('intervention_plan.actions.add_meeting'))
                        ->model(InterventionMeeting::class)
                        ->relationship(fn () => $this->record->meetings())
                        ->modalHeading(__('intervention_plan.actions.add_meeting'))
                        ->schema($this->getFormSchema())
                        ->icon('heroicon-o-plus-circle')
                        ->successRedirectUrl(fn () => InterventionServiceResource::getUrl('view_meetings', [
                            'parent' => $this->getRecord()->interventionService,
                            'record' => $this->getRecord(),
                        ])),
                ])
                ->schema([
                    RepeatableEntry::make('meetings')
                        ->hiddenLabel()
                        ->columns(3)
                        ->schema([
                            SectionHeader::make('meeting')
                                ->state(function (SectionHeader $component, $record) {
                                    $index = (int) explode('.', $component->getStatePath())[1];

                                    return __('intervention_plan.headings.meeting_repeater', [
                                        'number' => $index + 1,
                                    ]);
                                })
                                ->badge(fn (InterventionMeeting $record) => $record->status)
                                ->action(
                                    EditAction::make()
                                        ->modalHeading(__('general.action.edit'))
                                        ->schema($this->getFormSchema())
                                        ->fillForm(fn (InterventionMeeting $record) => $record->toArray())
                                        ->extraModalFooterActions(
                                            fn () => [
                                                \Filament\Actions\Action::make('delete')
                                                    ->label(__('intervention_plan.actions.delete_meeting'))
                                                    ->link()
                                                    ->color('danger')
                                                    ->cancelParentActions()
                                                    ->action(function (InterventionMeeting $record) {
                                                        $record->delete();
                                                        $this->redirect(InterventionServiceResource::getUrl('view_meetings', [
                                                            'parent' => $this->getRecord()->interventionService,
                                                            'record' => $this->getRecord(),
                                                        ]));
                                                    }),
                                            ]
                                        )
                                        ->modalExtraFooterActionsAlignment(Alignment::Left)
                                        ->action(fn (array $data, InterventionMeeting $record) => $record->update($data))
                                        ->successRedirectUrl(fn () => InterventionServiceResource::getUrl('view_meetings', [
                                            'parent' => $this->getRecord()->interventionService,
                                            'record' => $this->getRecord(),
                                        ]))
                                ),

                            TextEntry::make('date')
                                ->label(__('intervention_plan.labels.date'))
                                ->formatStateUsing(fn (InterventionMeeting $record, Carbon|string $state) => (\is_string($state) ? $state : $state->format('d.m.Y')).' '.$record->time?->format('H:i')),
                            TextEntry::make('duration')
                                ->label(__('intervention_plan.labels.duration')),
                            TextEntry::make('specialist.name_role')
                                ->label(__('intervention_plan.labels.responsible_specialist')),
                            TextEntry::make('observations')
                                ->label(__('intervention_plan.labels.observations'))
                                ->html()
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
    }
}
