<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages;

use App\Actions\BackAction;
use App\Enums\AggressorRelationship;
use App\Enums\Diseases;
use App\Enums\Ternary;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Infolists\Components\Actions\Edit;
use App\Infolists\Components\EnumEntry;
use App\Infolists\Components\SectionHeader;
use App\Models\Aggressor;
use App\Models\Beneficiary;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBeneficiaryPersonalInformation extends ViewRecord
{
    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return  __('beneficiary.page.personal_information.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_personal_information');
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns()
            ->schema(static::getPersonalInformationFormSchema());
    }

    public static function getPersonalInformationFormSchema(): array
    {
        return [
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make(__('beneficiary.section.personal_information.section.beneficiary'))
                        ->columns()
                        ->maxWidth('3xl')
                        ->schema(static::beneficiarySection()),

                    Tab::make(__('beneficiary.section.personal_information.section.aggressor'))
                        ->maxWidth('3xl')
                        ->schema(static::aggressorSection()),

                    Tab::make(__('beneficiary.section.personal_information.section.antecedents'))
                        ->maxWidth('3xl')
                        ->columns()
                        ->schema(static::antecedentsSection()),

                    Tab::make(__('beneficiary.section.personal_information.section.flow'))
                        ->maxWidth('3xl')
                        ->columns()
                        ->schema(static::flowSection()),
                ]),
        ];
    }

    protected static function beneficiarySection(): array
    {
        return [
            Section::make(__('beneficiary.section.personal_information.section.beneficiary'))
                ->columns()
                ->relationship('details')
                ->headerActions([
                    Edit::make('edit')
                        ->url(fn ($record) => BeneficiaryResource::getUrl('edit_personal_information', ['record' => $record])),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    EnumEntry::make('has_family_doctor')
                        ->label(__('field.has_family_doctor'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('family_doctor_name')
                        ->label(__('field.family_doctor_name'))
                        ->placeholder(__('placeholder.name')),

                    TextEntry::make('family_doctor_contact')
                        ->label(__('field.family_doctor_contact'))
                        ->placeholder(__('placeholder.phone_or_email')),

                    TextEntry::make('family_doctor_address')
                        ->label(__('field.family_doctor_address')),

                    Grid::make()
                        ->schema([
                            TextEntry::make('health_insurance')
                                ->label(__('beneficiary.section.personal_information.label.health_insurance')),

                            TextEntry::make('health_status')
                                ->label(__('beneficiary.section.personal_information.label.health_status')),

                            TextEntry::make('observations_chronic_diseases')
                                ->label(__('beneficiary.section.personal_information.label.observations_chronic_diseases'))
                                ->columnSpanFull()
                                ->visible(fn (Beneficiary $record) => $record->details->health_status?->contains(Diseases::CHRONIC_DISEASES)),

                            TextEntry::make('observations_degenerative_diseases')
                                ->label(__('beneficiary.section.personal_information.label.observations_degenerative_diseases'))
                                ->columnSpanFull()
                                ->visible(fn (Beneficiary $record) => $record->details->health_status?->contains(Diseases::DEGENERATIVE_DISEASES)),

                            TextEntry::make('observations_mental_illness')
                                ->label(__('beneficiary.section.personal_information.label.observations_mental_illness'))
                                ->columnSpanFull()
                                ->visible(fn (Beneficiary $record) => $record->details->health_status?->contains(Diseases::MENTAL_ILLNESSES)),
                        ]),

                    Grid::make()
                        ->schema([
                            EnumEntry::make('psychiatric_history')
                                ->label(__('field.psychiatric_history'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('psychiatric_history_notes')
                                ->label(__('field.psychiatric_history_notes')),

                            EnumEntry::make('investigations_for_psychiatric_pathology')
                                ->label(__('intervention_plan.labels.investigations_for_psychiatric_pathology'))
                                ->enumClass(Ternary::class),

                            TextEntry::make('investigations_observations')
                                ->label(__('intervention_plan.labels.investigations_observations')),

                            EnumEntry::make('treatment_for_psychiatric_pathology')
                                ->label(__('intervention_plan.labels.treatment_for_psychiatric_pathology'))
                                ->enumClass(Ternary::class),

                            TextEntry::make('treatment_observations')
                                ->label(__('intervention_plan.labels.treatment_observations')),
                        ]),

                    Grid::make()
                        ->schema([
                            TextEntry::make('disabilities')
                                ->label(__('beneficiary.section.personal_information.label.disabilities')),

                            TextEntry::make('type_of_disability')
                                ->label(__('beneficiary.section.personal_information.label.type_of_disability'))
                                ->visible(fn (Beneficiary $beneficiary) => Ternary::isYes($beneficiary->details->disabilities)),

                            TextEntry::make('degree_of_disability')
                                ->label(__('beneficiary.section.personal_information.label.degree_of_disability'))
                                ->visible(fn (Beneficiary $beneficiary) => Ternary::isYes($beneficiary->details->disabilities)),

                            TextEntry::make('observations_disability')
                                ->label(__('beneficiary.section.personal_information.label.observations_disability'))
                                ->visible(fn (Beneficiary $beneficiary) => Ternary::isYes($beneficiary->details->disabilities)),
                        ]),

                    Grid::make()
                        ->schema([
                            EnumEntry::make('current_contraception')
                                ->label(__('intervention_plan.labels.current_contraception'))
                                ->enumClass(Ternary::class),

                            TextEntry::make('observations_contraception')
                                ->label(__('intervention_plan.labels.observations_contraception')),

                        ]),
                    Grid::make()
                        ->schema([
                            EnumEntry::make('criminal_history')
                                ->label(__('field.criminal_history'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('criminal_history_notes')
                                ->label(__('field.criminal_history_notes')),
                        ]),

                    EnumEntry::make('studies')
                        ->label(__('field.studies'))
                        ->placeholder(__('placeholder.studies')),

                    EnumEntry::make('occupation')
                        ->label(__('field.occupation'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('workplace')
                        ->label(__('field.workplace'))
                        ->placeholder(__('placeholder.workplace'))
                        ->columnSpanFull(),

                    TextEntry::make('net_income')
                        ->label(__('field.net_income')),

                    EnumEntry::make('income')
                        ->label(__('field.income'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('income_source')
                        ->label(__('beneficiary.section.personal_information.label.income_source')),

                    TextEntry::make('elder_care_count')
                        ->label(__('field.elder_care_count'))
                        ->placeholder(__('placeholder.number'))
                        ->numeric(),

                    EnumEntry::make('homeownership')
                        ->label(__('field.homeownership'))
                        ->placeholder(__('placeholder.select_one')),
                ]),
        ];
    }

    protected static function aggressorSection(): array
    {
        return [
            Section::make(__('beneficiary.section.personal_information.section.aggressor'))
                ->columns()
                ->headerActions([
                    Edit::make('edit')
                        ->url(fn ($record) => BeneficiaryResource::getUrl('edit_aggressor', ['record' => $record])),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    RepeatableEntry::make('aggressors')
                        ->columns()
                        ->columnSpanFull()
                        ->hiddenLabel()
                        ->schema([
                            SectionHeader::make('header')
                                ->state(function (SectionHeader $component) {
                                    $index = (int) explode('.', $component->getStatePath())[1];

                                    return __('beneficiary.section.personal_information.heading.aggressor', [
                                        'number' => $index + 1,
                                    ]);
                                })
                                ->visible(
                                    fn (SectionHeader $component) => $component->getContainer()->getParentComponent()->getState()->count() > 1
                                ),

                            EnumEntry::make('relationship')
                                ->label(__('field.aggressor_relationship'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('relationship_other')
                                ->label(__('field.aggressor_relationship_other'))
                                ->visible(fn (Aggressor $record) => $record->relationship === AggressorRelationship::OTHER),

                            TextEntry::make('age')
                                ->label(__('field.aggressor_age'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),

                            EnumEntry::make('gender')
                                ->label(__('field.aggressor_gender'))
                                ->placeholder(__('placeholder.select_one')),

                            EnumEntry::make('citizenship')
                                ->label(__('field.aggressor_citizenship'))
                                ->placeholder(__('placeholder.citizenship')),

                            EnumEntry::make('civil_status')
                                ->label(__('field.aggressor_civil_status'))
                                ->placeholder(__('placeholder.civil_status')),

                            EnumEntry::make('studies')
                                ->label(__('field.aggressor_studies'))
                                ->placeholder(__('placeholder.studies')),

                            EnumEntry::make('occupation')
                                ->label(__('field.aggressor_occupation'))
                                ->placeholder(__('placeholder.select_one')),

                            Grid::make()
                                ->schema([
                                    EnumEntry::make('has_violence_history')
                                        ->label(__('field.aggressor_has_violence_history'))
                                        ->placeholder(__('placeholder.select_one')),

                                    TextEntry::make('violence_types')
                                        ->label(__('field.aggressor_violence_types')),

                                ]),

                            Grid::make()
                                ->schema([
                                    TextEntry::make('legal_history')
                                        ->label(__('field.aggressor_legal_history')),
                                    TextEntry::make('legal_history_notes')
                                        ->label(__('field.aggressor_legal_history_notes')),
                                ]),

                            Grid::make()
                                ->schema([
                                    TextEntry::make('has_protection_order')
                                        ->label(__('field.has_protection_order')),

                                    TextEntry::make('electronically_monitored')
                                        ->label(__('field.electronically_monitored')),

                                    TextEntry::make('protection_order_notes')
                                        ->label(__('field.protection_order_notes')),
                                ]),

                            Grid::make()
                                ->schema([
                                    EnumEntry::make('has_psychiatric_history')
                                        ->label(__('field.aggressor_has_psychiatric_history'))
                                        ->placeholder(__('placeholder.select_one')),

                                    TextEntry::make('psychiatric_history_notes')
                                        ->label(__('field.aggressor_psychiatric_history_notes')),
                                ]),

                            Grid::make()
                                ->schema([
                                    EnumEntry::make('has_drug_history')
                                        ->label(__('field.aggressor_has_drug_history'))
                                        ->placeholder(__('placeholder.select_one')),

                                    TextEntry::make('drugs')
                                        ->label(__('field.aggressor_drugs')),
                                ]),

                        ]),
                ]),
        ];
    }

    protected static function antecedentsSection(): array
    {
        return [
            Section::make(__('beneficiary.section.personal_information.section.antecedents'))
                ->columns()
                ->relationship('antecedents')
                ->headerActions([
                    Edit::make('edit')
                        ->url(fn ($record) => BeneficiaryResource::getUrl('edit_antecedents', ['record' => $record])),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    Grid::make()
                        ->schema([
                            EnumEntry::make('has_police_reports')
                                ->label(__('field.has_police_reports'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('police_report_count')
                                ->label(__('field.police_report_count'))
                                ->placeholder(__('placeholder.number')),
                        ]),

                    Grid::make()
                        ->schema([
                            EnumEntry::make('has_medical_reports')
                                ->label(__('field.has_medical_reports'))
                                ->placeholder(__('placeholder.select_one')),

                            TextEntry::make('medical_report_count')
                                ->label(__('field.medical_report_count'))
                                ->placeholder(__('placeholder.number'))
                                ->numeric(),
                        ]),

                    TextEntry::make('observations')
                        ->label(__('field.antecedents_observations'))
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected static function flowSection(): array
    {
        return [
            Section::make(__('beneficiary.section.personal_information.section.flow'))
                ->columns()
                ->relationship('flowPresentation')
                ->headerActions([
                    Edit::make('edit')
                        ->url(fn ($record) => BeneficiaryResource::getUrl('edit_flow_presentation', ['record' => $record])),
                ])
                ->extraAttributes([
                    'class' => 'h-full',
                ])
                ->schema([
                    Grid::make()
                        ->schema([
                            EnumEntry::make('presentation_mode')
                                ->label(__('field.presentation_mode'))
                                ->placeholder(__('placeholder.select_one')),

                            EnumEntry::make('referringInstitution.name')
                                ->label(__('field.referring_institution'))
                                ->placeholder(__('placeholder.select_one')),

                            EnumEntry::make('referral_mode')
                                ->label(__('field.referral_mode'))
                                ->placeholder(__('placeholder.select_one')),
                        ]),

                    EnumEntry::make('notifier')
                        ->label(__('field.notifier'))
                        ->placeholder(__('placeholder.select_one')),

                    EnumEntry::make('notification_mode')
                        ->label(__('field.notification_mode'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('notifier_other')
                        ->label(__('field.notifier_other')),

                    TextEntry::make('act_location')
                        ->label(__('field.act_location')),

                    TextEntry::make('act_location_other')
                        ->label(__('field.act_location_other')),

                    TextEntry::make('firstCalledInstitution.name')
                        ->label(__('field.first_called_institution'))
                        ->placeholder(__('placeholder.select_one')),

                    TextEntry::make('otherCalledInstitution.name')
                        ->label(__('field.other_called_institutions')),
                ]),
        ];
    }
}
