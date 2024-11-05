<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Pages;

use App\Concerns\HasGroupPages;
use App\Concerns\HasParentResource;
use App\Enums\MeetingStatus;
use App\Filament\Organizations\Resources\BeneficiaryInterventionResource;
use App\Forms\Components\Select;
use App\Infolists\Components\Actions\CreateAction;
use App\Infolists\Components\SectionHeader;
use App\Models\InterventionMeeting;
use App\Models\User;
use App\Services\Breadcrumb\InterventionPlanBreadcrumb;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class ViewMeetings extends ViewRecord
{
    use HasParentResource;
    use HasGroupPages;

    protected static string $resource = BeneficiaryInterventionResource::class;

    protected static string $view = 'filament.organizations.pages.view-beneficiary-interventions';

    public function getBreadcrumbs(): array
    {
        return InterventionPlanBreadcrumb::make($this->getRecord()->interventionService->interventionPlan)
            ->getInterventionBreadcrumb($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->organizationServiceIntervention->serviceInterventionWithoutStatusCondition->name;
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('status')
                ->label(__('intervention_plan.labels.status'))
                ->options(MeetingStatus::options()),
            DatePicker::make('date')
                ->label(__('intervention_plan.labels.date'))
                ->format('Y-m-d')
                ->native(false),
            TimePicker::make('time')
                ->label(__('intervention_plan.labels.time'))
                ->format('H:i')
                ->displayFormat('H:i')
                ->seconds(false),
            TextInput::make('duration')
                ->label(__('intervention_plan.labels.duration'))
                ->maxLength(3)
                ->numeric(),
            Select::make('user_id')
                ->label(__('intervention_plan.labels.responsible_specialist'))
                ->options(User::getTenantOrganizationUsers()),
            RichEditor::make('observations')
                ->label(__('intervention_plan.labels.observations'))
                ->columnSpanFull(),
            Hidden::make('beneficiary_intervention_id')
                ->default($this->record->id),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $this->getRecord()->load('meetings.user');

        return $infolist->schema([
            Section::make()
                ->key('meetings')
                ->headerActions([
                    CreateAction::make('create_meeting')
                        ->label(__('intervention_plan.actions.add_meeting'))
                        ->model(InterventionMeeting::class)
                        ->relationship(fn () => $this->record->meetings())
                        ->modalHeading(__('intervention_plan.actions.add_meeting'))
                        ->form($this->getFormSchema())
                        ->icon('heroicon-o-plus-circle'),
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
                                    ]) . ' ' . $record->status?->getLabel();
                                })
                                ->action(Action::make('edit')
                                    ->label(__('general.action.edit'))
                                    ->icon('heroicon-o-pencil')
                                    ->link()
                                    ->modalHeading(__('general.action.edit'))
                                    ->form($this->getFormSchema())
                                    ->fillForm(fn (InterventionMeeting $record) => $record->toArray())
                                    ->extraModalFooterActions(
                                        fn (InterventionMeeting $record) => [
                                            Action::make('delete')
//                                                ->record($record)
                                                ->label(__('intervention_plan.actions.delete_meeting'))
                                                ->outlined()
                                                ->color('danger')
                                                ->cancelParentActions()
                                                ->action(fn (InterventionMeeting $record) => $record->delete()),
                                        ]
                                    )->action(fn (array $data, InterventionMeeting $record) => $record->update($data))),

                            TextEntry::make('date')
                                ->label(__('intervention_plan.labels.date'))
                                ->formatStateUsing(fn (InterventionMeeting $record, Carbon $state) => $state->format('Y-m-d') . ' ' . $record->time?->format('H:i')),
                            TextEntry::make('duration')
                                ->label(__('intervention_plan.labels.duration')),
                            TextEntry::make('user.full_name')
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
