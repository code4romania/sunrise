<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Enums\Occupation;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Location;
use Awcodes\FilamentTableRepeater\Components\TableRepeater;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateDetailedEvaluation extends EditRecord
{
    use HasWizard;

    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumb(): string
    {
        return __('beneficiary.breadcrumb.wizard_detailed_evaluation');
    }

    /**
     * @return string|Htmlable
     */
    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.create_detailed_evaluation.title');
    }

    public function getSteps(): array
    {
        return [
            Step::make(__('beneficiary.wizard.detailed_evaluation.label'))
                ->schema([
                    TableRepeater::make('specialists')
                        ->relationship('specialists')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.specialists'))
                        ->defaultItems(3)
                        ->addActionLabel(__('beneficiary.action.add_row'))
                        ->showLabels(false)
                        ->deletable()
                        ->schema([
                            TextInput::make('full_name')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.full_name')),

                            TextInput::make('institution')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.institution')),

                            TextInput::make('relationship')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.relationship')),

                            DatePicker::make('date')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.contact_date')),
                        ]),

                    Repeater::make('meetings')
                        ->relationship('meetings')
                        ->columnSpan(1)
                        ->columns()
                        ->addActionLabel(__('beneficiary.action.add_meet_row'))
                        ->label(__('beneficiary.section.detailed_evaluation.labels.meetings'))
                        ->schema([
                            TextInput::make('specialist')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.specialist'))
                                ->placeholder(__('beneficiary.placeholder.full_name'))
                                ->required(),
                            DatePicker::make('date')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.date'))
                                ->placeholder(__('beneficiary.placeholder.date'))
                                ->required(),
                            TextInput::make('location')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.location'))
                                ->placeholder(__('beneficiary.placeholder.meet_location')),
                            TextInput::make('observations')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                                ->placeholder(__('beneficiary.placeholder.relevant_details')),

                        ]),
                ]),

            Step::make(__('beneficiary.wizard.partner.label'))
                ->schema([
                    Group::make([
                        Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                            ->schema([
                                TextInput::make('last_name')
                                    ->label(__('field.last_name'))
                                    ->placeholder(__('beneficiary.placeholder.last_name')),

                                TextInput::make('first_name')
                                    ->label(__('field.first_name'))
                                    ->placeholder(__('beneficiary.placeholder.first_name')),

                                TextInput::make('age')
                                    ->label(__('field.age'))
                                    ->placeholder(__('beneficiary.placeholder.age')),

                                Select::make('occupation')
                                    ->label(__('field.occupation'))
                                    ->placeholder(__('beneficiary.placeholder.occupation'))
                                    ->options(Occupation::options())
                                    ->enum(Occupation::class),

                                Location::make('legal_residence')
                                    ->city()
                                    ->address()
                                    ->environment(false),

                                Checkbox::make('same_as_legal_residence')
                                    ->label(__('field.same_as_legal_residence'))
                                    ->live()
                                    ->afterStateUpdated(function (bool $state, Set $set) {
                                        if ($state) {
                                            $set('effective_residence_county_id', null);
                                            $set('effective_residence_city_id', null);
                                            $set('effective_residence_address', null);
                                            $set('effective_residence_environment', null);
                                        }
                                    })
                                    ->columnSpanFull(),

                                Location::make('effective_residence')
                                    ->city()
                                    ->address()
                                    ->hidden(function (Get $get) {
                                        return $get('same_as_legal_residence');
                                    }),

                                Textarea::make('observations')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                                    ->placeholder(__('beneficiary.placeholder.partner_relevant_observations')),
                            ]),
                    ])
                        ->relationship('partner')
                        ->columns(),
                ]),

            Step::make(__('beneficiary.wizard.multidisciplinary_evaluation.label'))
                ->schema([
                    Group::make([
                        Section::make(__('beneficiary.section.detailed_evaluation.heading.reasons_for_start_evaluation'))
                            ->schema([
                                Select::make('applicant')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.applicant'))
                                    ->placeholder(__('beneficiary.placeholder.applicant'))
//                                    ->required()
                                    ->options([
                                        'aaaa' => 'aaaaa',
                                        'bbbb' => 'bbbbb',
                                    ]),
                                TextInput::make('reporting_by')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.reporting_by'))
                                    ->placeholder(__('beneficiary.placeholder.reporting_by')),
                            ])
                            ->columns(),

                        Section::make(__('beneficiary.section.detailed_evaluation.heading.historic_violence'))
                            ->schema([
                                // TODO: repeater
                                TextInput::make('date_interval')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.date_interval'))
                                    ->placeholder(__('beneficiary.placeholder.date_interval')),
                                MarkdownEditor::make('significant_events')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.significant_events'))
                                    ->placeholder(__('beneficiary.placeholder.significant_events')),
                            ]),

                        Section::make(__('beneficiary.section.detailed_evaluation.heading.beneficiary_needs'))
                            ->schema([
                                Textarea::make('medical_need')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.medical_need'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                                Textarea::make('professional_need')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.professional_need'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                                Textarea::make('emotional_and_psychological_need')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.emotional_and_psychological_need'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                                Textarea::make('social_economic_need')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.social_economic_need'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                                Textarea::make('legal_needs')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.legal_needs'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                            ]),

                        Section::make(__('beneficiary.section.detailed_evaluation.heading.family'))
                            ->schema([
                                Textarea::make('extended_family')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.extended_family'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                                Textarea::make('family_social_integration')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.family_social_integration'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                                Textarea::make('income')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.income'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                                Textarea::make('community_resources')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.community_resources'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                                Textarea::make('house')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.house'))
                                    ->placeholder(__('beneficiary.placeholder.need_description')),
                            ]),

                        Section::make(__('beneficiary.section.detailed_evaluation.heading.risk'))
                            ->schema([
                                Textarea::make('risk')
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.risk'))
                                    ->placeholder(__('beneficiary.placeholder.crisis_risk')),
                            ]),
                    ])
                        ->relationship('multidisciplinaryEvaluation'),
                ]),

            Step::make(__('beneficiary.wizard.results.label'))
                ->schema([
                    Group::make([
                        Checkbox::make('psychological_advice')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.psychological_advice')),
                        Checkbox::make('legal_advice')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.legal_advice')),
                        Checkbox::make('legal_assistance')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.legal_assistance')),
                        Checkbox::make('prenatal_advice')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.prenatal_advice')),
                        Checkbox::make('social_advice')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.social_advice')),
                        Checkbox::make('medical_services')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.medical_services')),
                        Checkbox::make('medical_payment')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.medical_payment')),
                        Checkbox::make('securing_residential_spaces')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.securing_residential_spaces')),
                        Checkbox::make('occupational_program_services')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.occupational_program_services')),
                        Checkbox::make('educational_services_for_children')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.educational_services_for_children')),
                        Checkbox::make('temporary_shelter_services')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.temporary_shelter_services')),
                        Checkbox::make('protection_order')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.protection_order')),
                        Checkbox::make('crisis_assistance')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.crisis_assistance')),
                        Checkbox::make('safety_plan')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.safety_plan')),
                        Checkbox::make('other_services')
                            ->label(__('beneficiary.section.detailed_evaluation.labels.other_services')),
                        Textarea::make('other_services_description')
                            ->label('')
                            ->placeholder(__('beneficiary.placeholder.other_services')),
                        Section::make(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                            ->schema([
                                MarkdownEditor::make('recommendations_for_intervention_plan')
                                    ->helperText(__('beneficiary.helper_text.recommendations_for_intervention_plan'))
                                    ->label(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                                    ->placeholder(__('beneficiary.placeholder.other_services')),
                            ]),

                    ])
                        ->relationship('detailedEvaluationResult'),
                ]),
        ];
    }
}
