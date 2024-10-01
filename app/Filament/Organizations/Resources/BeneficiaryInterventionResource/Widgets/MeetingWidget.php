<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryInterventionResource\Widgets;

use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Models\BeneficiaryIntervention;
use App\Models\InterventionMeeting;
use App\Widgets\FormWidget;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Database\Eloquent\Model;

class MeetingWidget extends FormWidget
{
    use InteractsWithFormActions;
    use InteractsWithActions;

    public ?BeneficiaryIntervention $record = null;

    // TODO: fix form validation
    public function form(Form $form): Form
    {
        return $form->schema($this->getFormSchema())
            ->model($this->getFormModel());
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('meetings')
                ->hiddenLabel()
                ->schema([
                    //                    Repeater::make('meetings')
                    //                        ->columns(3)
                    //                        ->relationship('meetings')
                    //                        ->schema([
                    Select::make('status')
                        ->options(['aaaaa', 'bbbbb']),
                    DatePicker::make('date'),
                    TimePicker::make('time'),
                    TextInput::make('duration'),
                    Select::make('user_id'),
                    RichEditor::make('observations')
                        ->columnSpanFull(),
                    //                        ]),
                ]),

        ];
    }

    protected function getFormModel(): Model|string|null
    {
        $model = new InterventionMeeting();
        $model->beneficiary_intervention_id = $this->record->id;

        return $model;
//        dd(InterventionMeeting::(['beneficiary_intervention_id' => $this->record->id]));
//        return ;
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->extraAttributes(['class' => 'float-right'])
                ->action('save')
                ->color('primary'),
        ];
    }

    public function save()
    {
        dd($this->form->getState());
        $formData = $this->form->getState();
        dd($formData);
        dd($this->form);
    }

    protected function getInfolistSchema(): array
    {
        return [
            RepeatableEntry::make('meetings')
                ->schema([
                    TextEntry::make('status'),
                    TextEntry::make('date'),
                    TextEntry::make('time'),
                    TextEntry::make('duration'),
                    TextEntry::make('user_id'),
                    TextEntry::make('observations')
                        ->columnSpanFull(),
                ]),
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
