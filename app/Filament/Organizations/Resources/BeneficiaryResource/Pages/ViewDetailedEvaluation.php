<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\Location;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDetailedEvaluation extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbsForDetailedEvaluation();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tabs\Tab::make(__('beneficiary.wizard.detailed_evaluation.label'))
                            ->maxWidth('3xl')
                            ->schema([
                                Section::make(__('beneficiary.wizard.detailed_evaluation.label'))
                                    ->headerActions([
                                        BeneficiaryResource\Actions\Edit::make('edit')
                                            ->url(fn ($record) => BeneficiaryResource::getUrl(
                                                'edit_detailed_evaluation',
                                                ['record' => $record]
                                            )),
                                    ])
                                    ->schema($this->getDetailedEvaluationSchema()),
                            ]),

                        Tabs\Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                            ->maxWidth('3xl')
                            ->schema(ViewBeneficiaryIdentity::identitySchemaForOtherPage($this->record)),

                        Tabs\Tab::make(__('beneficiary.section.identity.tab.children'))
                            ->maxWidth('3xl')
                            ->schema(ViewBeneficiaryIdentity::childrenSchemaForOtherPage($this->record)),

                        Tabs\Tab::make(__('beneficiary.wizard.partner.label'))
                            ->maxWidth('3xl')
                            ->schema([
                                Group::make()
                                    ->relationship('partner')
                                    ->columns()
                                    ->schema([
                                        Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                                            ->headerActions([
                                                BeneficiaryResource\Actions\Edit::make('edit')
                                                    ->url(fn ($record) => BeneficiaryResource::getUrl(
                                                        'edit_beneficiary_partner',
                                                        ['record' => $record]
                                                    )),
                                            ])
                                            ->schema($this->getPartnerSchema()),
                                    ]),
                            ]),

                        Tabs\Tab::make(__('beneficiary.wizard.multidisciplinary_evaluation.label'))
                            ->maxWidth('3xl')
                            ->schema([
                                Section::make(__('beneficiary.wizard.multidisciplinary_evaluation.label'))
                                    ->headerActions([
                                        BeneficiaryResource\Actions\Edit::make('edit')
                                            ->url(fn ($record) => BeneficiaryResource::getUrl(
                                                'edit_multidisciplinary_evaluation',
                                                ['record' => $record]
                                            )),
                                    ])
                                    ->schema($this->getMultidisciplinarySchema()),
                            ]),

                        Tabs\Tab::make(__('beneficiary.wizard.results.label'))
                            ->maxWidth('3xl')
                            ->schema([
                                Group::make()
                                    ->relationship('detailedEvaluationResult')
                                    ->schema([
                                        Section::make(__('beneficiary.wizard.results.label'))
                                            ->headerActions([
                                                BeneficiaryResource\Actions\Edit::make('edit')
                                                    ->url(fn ($record) => BeneficiaryResource::getUrl(
                                                        'edit_detailed_evaluation_result',
                                                        ['record' => $record]
                                                    )),
                                            ])
                                            ->schema($this->getResultSchema()),
                                    ]),
                            ]),

                    ]),
            ]);
    }

    private function getResultSchema(): array
    {
        return [
            TextEntry::make('psychological_advice')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.psychological_advice')),
            TextEntry::make('legal_advice')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_advice')),
            TextEntry::make('legal_assistance')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_assistance')),
            TextEntry::make('prenatal_advice')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.prenatal_advice')),
            TextEntry::make('social_advice')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.social_advice')),
            TextEntry::make('medical_services')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_services')),
            TextEntry::make('medical_payment')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_payment')),
            TextEntry::make('securing_residential_spaces')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.securing_residential_spaces')),
            TextEntry::make('occupational_program_services')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.occupational_program_services')),
            TextEntry::make('educational_services_for_children')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.educational_services_for_children')),
            TextEntry::make('temporary_shelter_services')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.temporary_shelter_services')),
            TextEntry::make('protection_order')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.protection_order')),
            TextEntry::make('crisis_assistance')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.crisis_assistance')),
            TextEntry::make('safety_plan')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.safety_plan')),
            TextEntry::make('other_services')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('beneficiary.section.detailed_evaluation.labels.other_services')),
            TextEntry::make('other_services_description')
                ->label(__('beneficiary.section.detailed_evaluation.labels.other_services'))
                ->placeholder(__('beneficiary.placeholder.other_services')),
            Section::make(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                ->schema([
                    TextEntry::make('recommendations_for_intervention_plan')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                        ->placeholder(__('beneficiary.placeholder.other_services')),
                ]),
        ];
    }

    public function getMultidisciplinarySchema(): array
    {
        return [
            Group::make()
                ->relationship('multidisciplinaryEvaluation')
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.reasons_for_start_evaluation'))
                        ->schema([
                            TextEntry::make('applicant')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.applicant'))
                                ->formatStateUsing(fn ($state) => $state == '-' ? $state : $state->label())
                                ->placeholder(__('beneficiary.placeholder.applicant')),
                            TextEntry::make('reporting_by')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.reporting_by'))
                                ->placeholder(__('beneficiary.placeholder.reporting_by')),
                        ])
                        ->columns(),

                ]),
            Section::make(__('beneficiary.section.detailed_evaluation.heading.historic_violence'))
                ->schema([
                    RepeatableEntry::make('violenceHistory')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('date_interval')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.date_interval'))
                                ->placeholder(__('beneficiary.placeholder.date_interval')),
                            TextEntry::make('significant_events')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.significant_events'))
                                ->placeholder(__('beneficiary.placeholder.significant_events'))
                                ->html(),
                        ]),
                ]),

            Group::make()
                ->relationship('multidisciplinaryEvaluation')
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.beneficiary_needs'))
                        ->schema([
                            TextEntry::make('medical_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.medical_need'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                            TextEntry::make('professional_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.professional_need'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                            TextEntry::make('emotional_and_psychological_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.emotional_and_psychological_need'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                            TextEntry::make('social_economic_need')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.social_economic_need'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                            TextEntry::make('legal_needs')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.legal_needs'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                        ]),

                    Section::make(__('beneficiary.section.detailed_evaluation.heading.family'))
                        ->schema([
                            TextEntry::make('extended_family')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.extended_family'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                            TextEntry::make('family_social_integration')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.family_social_integration'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                            TextEntry::make('income')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.income'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                            TextEntry::make('community_resources')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.community_resources'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                            TextEntry::make('house')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.house'))
                                ->placeholder(__('beneficiary.placeholder.need_description')),
                        ]),

                    Section::make(__('beneficiary.section.detailed_evaluation.heading.risk'))
                        ->schema([
                            TextEntry::make('risk')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.risk'))
                                ->placeholder(__('beneficiary.placeholder.crisis_risk')),
                        ]),
                ]),
        ];
    }

    public function getPartnerSchema(): array
    {
        return [
            TextEntry::make('last_name')
                ->label(__('field.last_name'))
                ->placeholder(__('beneficiary.placeholder.last_name')),

            TextEntry::make('first_name')
                ->label(__('field.first_name'))
                ->placeholder(__('beneficiary.placeholder.first_name')),

            TextEntry::make('age')
                ->label(__('field.age'))
                ->placeholder(__('beneficiary.placeholder.age')),

            TextEntry::make('occupation')
                ->label(__('field.occupation'))
                ->formatStateUsing(fn ($state) => $state == '-' ? $state : $state->label())
                ->placeholder(__('beneficiary.placeholder.occupation')),

            Location::make('legal_residence')
                ->city()
                ->address()
                ->environment(false),

            TextEntry::make('same_as_legal_residence')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('field.same_as_legal_residence'))
                ->columnSpanFull(),

            Location::make('effective_residence')
                ->city()
                ->address(),

            TextEntry::make('observations')
                ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                ->placeholder(__('beneficiary.placeholder.partner_relevant_observations')),
        ];
    }

    public function getDetailedEvaluationSchema(): array
    {
        return [
            RepeatableEntry::make('specialists')
                ->columns(4)
                ->schema([
                    TextEntry::make('full_name')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.full_name')),

                    TextEntry::make('institution')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.institution')),

                    TextEntry::make('relationship')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.relationship')),

                    TextEntry::make('date')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.contact_date')),
                ]),
            //
            RepeatableEntry::make('meetings')
                ->columns()
                ->label(__('beneficiary.section.detailed_evaluation.labels.meetings'))
                ->schema([
                    TextEntry::make('specialist')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.specialist'))
                        ->placeholder(__('beneficiary.placeholder.full_name')),
                    TextEntry::make('date')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.date'))
                        ->placeholder(__('beneficiary.placeholder.date')),
                    TextEntry::make('location')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.location'))
                        ->placeholder(__('beneficiary.placeholder.meet_location')),
                    TextEntry::make('observations')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                        ->placeholder(__('beneficiary.placeholder.relevant_details')),

                ]),
        ];
    }
}
