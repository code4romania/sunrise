<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Widgets;

use App\Enums\MeetingStatus;
use App\Forms\Components\Select;
use App\Infolists\Components\SectionHeader;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionMeeting;
use App\Models\User;
use App\Widgets\InfolistWidget;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Concerns\InteractsWithFormActions;

class MeetingWidget extends InfolistWidget
{
    use InteractsWithFormActions;
    use InteractsWithActions;

    public ?BeneficiaryIntervention $record = null;

    protected function getFormSchema(): array
    {
        return [
            Select::make('status')
                ->label(__('intervention_plan.labels.status'))
                ->options(MeetingStatus::options()),
            DatePicker::make('date')
                ->label(__('intervention_plan.labels.date')),
            TimePicker::make('time')
                ->label(__('intervention_plan.labels.time')),
            TextInput::make('duration')
                ->label(__('intervention_plan.labels.duration')),
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

    protected function getInfolistSchema(): array
    {
        $this->record->load('meetings.user');

        return [
            RepeatableEntry::make('meetings')
                ->hiddenLabel()
                ->schema([
                    Section::make()
                        ->columns(3)
                        ->schema([
                            SectionHeader::make('header')
                                ->state(function (SectionHeader $component, $record) {
                                    $index = (int) explode('.', $component->getStatePath())[1];

                                    return __('intervention_plan.headings.meeting_repeater', [
                                        'number' => $index + 1,
                                    ]) . ' ' . $record->status?->getLabel();
                                })
                                ->action(
                                    \Filament\Infolists\Components\Actions\Action::make('edit')
                                        ->label(__('general.action.edit'))
                                        ->icon('heroicon-o-pencil')
                                        ->link()
                                        ->modalHeading(__('general.action.edit'))
                                        ->form($this->getFormSchema())
                                        ->fillForm(fn ($record) => $record)
                                        ->action(fn () => dd('aaaa'))
                                ),
                            TextEntry::make('date')
                                ->label(__('intervention_plan.labels.date'))
                                ->formatStateUsing(fn (InterventionMeeting $record, $state) => $state . ' ' . $record->time),
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
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            CreateAction::make('create_meeting')
                ->label(__('intervention_plan.actions.add_meeting'))
                ->createAnother(false)
                ->model(InterventionMeeting::class)
                ->relationship(fn () => $this->record->meetings())
                ->modalHeading(__('intervention_plan.actions.add_meeting'))
                ->form($this->getFormSchema()),
        ];
    }

    protected function configureAction(Action $action): void
    {
//        dd('aaaa');
        $action
            ->record($this->record);
//            ->recordTitle($this->getRecordTitle());
    }
}
