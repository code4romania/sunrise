<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Concerns;

use App\Enums\MeetingStatus;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIntervention;
use App\Models\Specialist;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Grid;

trait InteractsWithInterventionMeetingForm
{
    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function interventionMeetingFormSchema(?Beneficiary $beneficiary): array
    {
        $specialistOptions = $beneficiary instanceof Beneficiary
            ? $beneficiary->specialistsTeam()
                ->with(['user:id,first_name,last_name', 'roleForDisplay:id,name'])
                ->get()
                ->mapWithKeys(fn (Specialist $s) => [$s->id => $s->name_role])
                ->all()
            : [];

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
                        ->options($specialistOptions)
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

    /**
     * @return array<string, mixed>
     */
    protected function interventionMeetingFormDefaultState(?BeneficiaryIntervention $intervention): array
    {
        return [
            'status' => MeetingStatus::PLANED,
            'date' => now()->format('Y-m-d'),
            'time' => null,
            'duration' => 60,
            'specialist_id' => $intervention?->specialist_id,
            'topic' => null,
            'observations' => null,
        ];
    }
}
