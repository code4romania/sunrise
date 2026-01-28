<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs\Tab;
use App\Actions\BackAction;
use App\Enums\AddressType;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiaryIdentity;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\DateEntry;
use App\Infolists\Components\Location;
use App\Infolists\Components\TableEntry;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Schemas\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewDetailedEvaluation extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.view_detailed_evaluation.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_detailed_evaluation');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make(__('beneficiary.wizard.detailed_evaluation.label'))
                            ->maxWidth('3xl')
                            ->schema([
                                Section::make(__('beneficiary.wizard.detailed_evaluation.label'))
                                    ->headerActions([
                                        EditAction::make()
                                            ->url(fn ($record) => BeneficiaryResource::getUrl(
                                                'edit_detailed_evaluation',
                                                ['record' => $record]
                                            )),
                                    ])
                                    ->schema($this->getDetailedEvaluationSchema()),
                            ]),

                        Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                            ->maxWidth('3xl')
                            ->schema(ViewBeneficiaryIdentity::identitySchemaForOtherPage($this->record)),

                        Tab::make(__('beneficiary.section.identity.tab.children'))
                            ->maxWidth('3xl')
                            ->schema(ViewBeneficiaryIdentity::childrenSchemaForOtherPage($this->record)),

                        Tab::make(__('beneficiary.wizard.partner.label'))
                            ->maxWidth('3xl')
                            ->schema([
                                Group::make()
                                    ->relationship('partner')
                                    ->columns()
                                    ->schema([
                                        Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                                            ->headerActions([
                                                EditAction::make()
                                                    ->url(fn ($record) => BeneficiaryResource::getUrl(
                                                        'edit_beneficiary_partner',
                                                        ['record' => $record]
                                                    )),
                                            ])
                                            ->schema($this->getPartnerSchema()),
                                    ]),
                            ]),

                        Tab::make(__('beneficiary.wizard.multidisciplinary_evaluation.label'))
                            ->maxWidth('3xl')
                            ->schema([
                                Section::make(__('beneficiary.wizard.multidisciplinary_evaluation.label'))
                                    ->headerActions([
                                        EditAction::make()
                                            ->url(fn ($record) => BeneficiaryResource::getUrl(
                                                'edit_multidisciplinary_evaluation',
                                                ['record' => $record]
                                            )),
                                    ])
                                    ->schema($this->getMultidisciplinarySchema()),
                            ]),

                        Tab::make(__('beneficiary.wizard.results.label'))
                            ->maxWidth('3xl')
                            ->schema([
                                Group::make()
                                    ->relationship('detailedEvaluationResult')
                                    ->schema([
                                        Section::make(__('beneficiary.wizard.results.label'))
                                            ->headerActions([
                                                EditAction::make()
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
            TextEntry::make('recommendation_services')
                ->label(__('beneficiary.section.detailed_evaluation.heading.recommendation_services'))
                ->listWithLineBreaks(),

            TextEntry::make('other_services_description')
                ->label(__('beneficiary.section.detailed_evaluation.labels.other_services'))
                ->placeholder(__('beneficiary.placeholder.other_services')),

            Section::make(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                ->compact()
                ->schema([
                    TextEntry::make('recommendations_for_intervention_plan')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                        ->hiddenLabel()
                        ->placeholder(__('beneficiary.placeholder.other_services'))
                        ->html(),
                ]),
        ];
    }

    public function getMultidisciplinarySchema(): array
    {
        return [

            Section::make(__('beneficiary.section.detailed_evaluation.heading.reasons_for_start_evaluation'))
                ->relationship('multidisciplinaryEvaluation')
                ->compact()
                ->columns()
                ->schema([
                    TextEntry::make('applicant')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.applicant'))
                        ->formatStateUsing(fn ($state) => $state == '-' ? $state : $state->label())
                        ->placeholder(__('beneficiary.placeholder.applicant')),

                    TextEntry::make('reporting_by')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.reporting_by'))
                        ->placeholder(__('beneficiary.placeholder.reporting_by')),
                ]),

            Section::make(__('beneficiary.section.detailed_evaluation.heading.historic_violence'))
                ->compact()
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
                        ->compact()
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
                        ->compact()
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
                        ->compact()
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

            Location::make(AddressType::LEGAL_RESIDENCE->value)
                ->relationship(AddressType::LEGAL_RESIDENCE->value)
                ->city()
                ->address()
                ->environment(false),

            TextEntry::make('same_as_legal_residence')
                ->formatStateUsing(fn ($state) => $state ? __('enum.ternary.1') : __('enum.ternary.0'))
                ->label(__('field.same_as_legal_residence'))
                ->columnSpanFull(),

            Location::make(AddressType::EFFECTIVE_RESIDENCE->value)
                ->relationship(AddressType::EFFECTIVE_RESIDENCE->value)
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
            Section::make(__('beneficiary.section.detailed_evaluation.labels.specialists'))
                ->compact()
                ->schema([
                    TableEntry::make('detailedEvaluationSpecialists')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('full_name')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.full_name'))
                                ->hiddenLabel(),

                            TextEntry::make('institution')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.institution'))
                                ->hiddenLabel(),

                            TextEntry::make('relationship')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.relationship'))
                                ->hiddenLabel(),

                            DateEntry::make('date')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.contact_date'))
                                ->hiddenLabel(),
                        ]),
                ]),

            Section::make(__('beneficiary.section.detailed_evaluation.labels.meetings'))
                ->compact()
                ->schema([
                    RepeatableEntry::make('meetings')
                        ->columns()
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('specialist')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.specialist'))
                                ->placeholder(__('beneficiary.placeholder.full_name')),

                            DateEntry::make('date')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.date')),

                            TextEntry::make('location')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.location'))
                                ->placeholder(__('beneficiary.placeholder.meet_location')),

                            TextEntry::make('observations')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                                ->placeholder(__('beneficiary.placeholder.relevant_details')),

                        ]),
                ]),
        ];
    }
}
