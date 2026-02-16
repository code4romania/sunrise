<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditCaseDetailedEvaluationDetails extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.wizard.details.label');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record->getBreadcrumb(),
            CaseResource::getUrl('view_detailed_evaluation', ['record' => $record]) => __('beneficiary.breadcrumb.wizard_detailed_evaluation'),
            '' => __('beneficiary.wizard.details.label'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_detailed_evaluation', ['record' => $this->getRecord()])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return CaseResource::getUrl('view_detailed_evaluation', ['record' => $this->getRecord()]).'?tab='.\Illuminate\Support\Str::slug(__('beneficiary.wizard.details.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->maxWidth('3xl')
                    ->schema([
                        Repeater::make('detailedEvaluationSpecialists')
                            ->relationship('detailedEvaluationSpecialists')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.specialists'))
                            ->addActionLabel(__('beneficiary.action.add_row'))
                            ->deletable()
                            ->columns(4)
                            ->itemLabel(fn (array $state): ?string => $state['full_name'] ?? null)
                            ->schema([
                                TextInput::make('full_name')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.full_name'))
                                    ->maxLength(50),
                                TextInput::make('institution')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.institution'))
                                    ->maxLength(50)
                                    ->default(fn () => \Filament\Facades\Filament::getTenant()?->institution?->name),
                                TextInput::make('relationship')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.relationship'))
                                    ->maxLength(50),
                                DatePicker::make('date')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.contact_date')),
                            ]),
                        Repeater::make('meetings')
                            ->relationship('meetings')
                            ->columns()
                            ->addActionLabel(__('beneficiary.action.add_meet_row'))
                            ->label(__('beneficiary.section.detailed_evaluation.labels.meetings'))
                            ->schema([
                                TextInput::make('specialist')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.specialist'))
                                    ->placeholder(__('beneficiary.placeholder.full_name'))
                                    ->maxLength(50)
                                    ->required(),
                                DatePicker::make('date')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.date'))
                                    ->placeholder(__('beneficiary.placeholder.date'))
                                    ->required(),
                                TextInput::make('location')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.location'))
                                    ->placeholder(__('beneficiary.placeholder.meet_location'))
                                    ->maxLength(50),
                                TextInput::make('observations')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                                    ->placeholder(__('beneficiary.placeholder.relevant_details'))
                                    ->maxLength(200),
                            ]),
                    ]),
            ]);
    }
}
