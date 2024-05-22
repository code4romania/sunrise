<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Enums\Applicant;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Livewire\Component;

class EditMultidisciplinaryEvaluation extends EditRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Group::make()
                ->relationship('multidisciplinaryEvaluation')
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.reasons_for_start_evaluation'))
                        ->schema([
                            Select::make('applicant')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.applicant'))
                                ->placeholder(__('beneficiary.placeholder.applicant'))
                                ->required()
                                ->live()
                                ->options(Applicant::options()),
                            TextInput::make('reporting_by')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.reporting_by'))
                                ->placeholder(__('beneficiary.placeholder.reporting_by'))
                                ->default(fn (Component $livewire) => $livewire->record->referringInstitution?->name)
                                ->visible(fn (Get $get) => Applicant::OTHER->is($get('applicant'))),
                        ])
                        ->columns(),

                    Section::make(__('beneficiary.section.detailed_evaluation.heading.historic_violence'))
                        ->schema([
                            // TODO: repeater
                            TextInput::make('date_interval')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.date_interval'))
                                ->placeholder(__('beneficiary.placeholder.date_interval'))
                                ->maxLength(100),
                            RichEditor::make('significant_events')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.significant_events'))
                                ->placeholder(__('beneficiary.placeholder.significant_events'))
                                ->maxLength(2000),
                        ]),

                    Section::make(__('beneficiary.section.detailed_evaluation.heading.beneficiary_needs'))
                        ->schema([
                            Textarea::make('medical_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_need'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                            Textarea::make('professional_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.professional_need'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                            Textarea::make('emotional_and_psychological_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.emotional_and_psychological_need'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                            Textarea::make('social_economic_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.social_economic_need'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                            Textarea::make('legal_needs')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_needs'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                        ]),

                    Section::make(__('beneficiary.section.detailed_evaluation.heading.family'))
                        ->schema([
                            Textarea::make('extended_family')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.extended_family'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                            Textarea::make('family_social_integration')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.family_social_integration'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                            Textarea::make('income')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.income'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                            Textarea::make('community_resources')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.community_resources'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                            Textarea::make('house')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.house'))
                                ->placeholder(__('beneficiary.placeholder.need_description'))
                                ->maxLength(1000),
                        ]),

                    Section::make(__('beneficiary.section.detailed_evaluation.heading.risk'))
                        ->schema([
                            Textarea::make('risk')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.risk'))
                                ->placeholder(__('beneficiary.placeholder.crisis_risk'))
                                ->maxLength(2000),
                        ]),
                ]),
        ];
    }
}
