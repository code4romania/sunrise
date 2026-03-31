<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation;

use App\Actions\BackAction;
use App\Concerns\PreventMultipleSubmit;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Enums\Applicant;
use App\Enums\Occupation;
use App\Enums\RecommendationService;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\CountyCitySelect;
use App\Forms\Components\DatePicker;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Models\Beneficiary;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Contracts\Support\Htmlable;

class CreateCaseDetailedEvaluation extends EditRecord
{
    use HasWizard;
    use PreventMultipleSubmit;
    use PreventSubmitFormOnEnter;
    use SavesPartnerAddresses;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.create_detailed_evaluation.title');
    }

    protected function getRedirectUrl(): string
    {
        return CaseResource::getUrl('view_detailed_evaluation', ['record' => $this->getRecord()]);
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        $breadcrumbs = [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
        ];
        if ($record instanceof Beneficiary) {
            $breadcrumbs[CaseResource::getUrl('view', ['record' => $record])] = $record->getBreadcrumb();
        }
        $breadcrumbs[''] = __('beneficiary.breadcrumb.wizard_detailed_evaluation');

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $this->getRecord()])),
        ];
    }

    public function getSteps(): array
    {
        return [
            Step::make(__('beneficiary.wizard.detailed_evaluation.label'))
                ->schema($this->getDetailedEvaluationStepSchema()),

            Step::make(__('beneficiary.wizard.partner.label'))
                ->schema($this->getPartnerStepSchema()),

            Step::make(__('beneficiary.wizard.multidisciplinary_evaluation.label'))
                ->schema($this->getMultidisciplinaryStepSchema()),

            Step::make(__('beneficiary.wizard.results.label'))
                ->schema($this->getResultsStepSchema()),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->mutateFormDataBeforeFillPartner($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->capturePartnerAddressDataBeforeSave($data);
    }

    public function afterSave(): void
    {
        $this->afterSavePartnerAddresses();
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected function getDetailedEvaluationStepSchema(): array
    {
        return [
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
                        ->collapsible(false)
                        ->schema([
                            TextInput::make('full_name')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.full_name'))
                                ->maxLength(50),
                            TextInput::make('institution')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.institution'))
                                ->maxLength(50)
                                ->default(fn () => Filament::getTenant()?->institution?->name),
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
                        ->collapsible(false)
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
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected function getPartnerStepSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.detailed_evaluation.heading.partner'))
                ->relationship('partner')
                ->maxWidth('3xl')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('last_name')
                                ->label(__('field.last_name'))
                                ->placeholder(__('beneficiary.placeholder.partner_last_name'))
                                ->maxLength(50),
                            TextInput::make('first_name')
                                ->label(__('field.first_name'))
                                ->placeholder(__('beneficiary.placeholder.partner_first_name'))
                                ->maxLength(50),
                            TextInput::make('age')
                                ->label(__('field.age'))
                                ->placeholder(__('beneficiary.placeholder.partner_age'))
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(99)
                                ->validationAttribute(__('field.age')),
                            Select::make('occupation')
                                ->label(__('field.occupation'))
                                ->placeholder(__('beneficiary.placeholder.occupation'))
                                ->options(Occupation::options())
                                ->enum(Occupation::class),
                        ]),
                    Grid::make()
                        ->schema([
                            ...CountyCitySelect::make()
                                ->countyField('legal_residence.county_id')
                                ->cityField('legal_residence.city_id')
                                ->countyLabel(__('field.legal_residence_county'))
                                ->cityLabel(__('field.legal_residence_city'))
                                ->countyPlaceholder(__('placeholder.county'))
                                ->cityPlaceholder(__('placeholder.city'))
                                ->required(false)
                                ->countyAfterStateUpdated(function (Set $set, Get $get): void {
                                    if ($get('same_as_legal_residence')) {
                                        $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                        $set('effective_residence.city_id', null);
                                    }
                                })
                                ->cityAfterStateUpdated(function (Set $set, Get $get, $state): void {
                                    if ($get('same_as_legal_residence')) {
                                        $set('effective_residence.city_id', $state);
                                    }
                                })
                                ->schema(),
                            TextInput::make('legal_residence.address')
                                ->label(__('field.legal_residence_address'))
                                ->placeholder(__('placeholder.address'))
                                ->maxLength(50),
                        ]),
                    Checkbox::make('same_as_legal_residence')
                        ->label(__('field.same_as_legal_residence'))
                        ->live()
                        ->afterStateUpdated(function (bool $state, Set $set, Get $get): void {
                            if (! $state) {
                                $set('effective_residence.county_id', null);
                                $set('effective_residence.city_id', null);
                                $set('effective_residence.address', null);
                            }
                            if ($state) {
                                $set('effective_residence.county_id', $get('legal_residence.county_id'));
                                $set('effective_residence.city_id', $get('legal_residence.city_id'));
                                $set('effective_residence.address', $get('legal_residence.address'));
                            }
                        })
                        ->columnSpanFull(),
                    Grid::make()
                        ->schema([
                            ...CountyCitySelect::make()
                                ->countyField('effective_residence.county_id')
                                ->cityField('effective_residence.city_id')
                                ->countyLabel(__('field.effective_residence_county'))
                                ->cityLabel(__('field.effective_residence_city'))
                                ->countyPlaceholder(__('placeholder.county'))
                                ->cityPlaceholder(__('placeholder.city'))
                                ->required(false)
                                ->countyDisabled(fn (Get $get): bool => (bool) $get('same_as_legal_residence'))
                                ->cityDisabled(fn (Get $get): bool => $get('same_as_legal_residence') || ! $get('effective_residence.county_id'))
                                ->schema(),
                            TextInput::make('effective_residence.address')
                                ->label(__('field.effective_residence_address'))
                                ->placeholder(__('placeholder.address'))
                                ->maxLength(50)
                                ->disabled(fn (Get $get): bool => (bool) $get('same_as_legal_residence')),
                        ]),
                    Textarea::make('observations')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.observations'))
                        ->placeholder(__('beneficiary.placeholder.partner_relevant_observations'))
                        ->maxLength(500)
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected function getMultidisciplinaryStepSchema(): array
    {
        return [
            Section::make(__('beneficiary.section.detailed_evaluation.heading.reasons_for_start_evaluation'))
                ->relationship('multidisciplinaryEvaluation')
                ->maxWidth('3xl')
                ->compact()
                ->schema([
                    Select::make('applicant')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.applicant'))
                        ->placeholder(__('beneficiary.placeholder.applicant'))
                        ->required()
                        ->live()
                        ->default(Applicant::BENEFICIARY)
                        ->enum(Applicant::class)
                        ->options(Applicant::options()),
                    TextInput::make('reporting_by')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.reporting_by'))
                        ->placeholder(__('beneficiary.placeholder.reporting_by'))
                        ->maxLength(255)
                        ->default(fn () => $this->getRecord()?->flowPresentation?->referringInstitution?->name)
                        ->required(fn (Get $get): bool => Applicant::OTHER->is($get('applicant'))),
                ]),
            Section::make(__('beneficiary.section.detailed_evaluation.heading.historic_violence'))
                ->maxWidth('3xl')
                ->compact()
                ->schema([
                    Repeater::make('violenceHistory')
                        ->relationship('violenceHistory')
                        ->hiddenLabel()
                        ->addActionLabel(__('beneficiary.action.add_violence_history'))
                        ->schema([
                            TextInput::make('date_interval')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.date_interval'))
                                ->placeholder(__('beneficiary.placeholder.date_interval'))
                                ->maxLength(100),
                            RichEditor::make('significant_events')
                                ->label(__('beneficiary.section.detailed_evaluation.labels.significant_events'))
                                ->placeholder(__('beneficiary.placeholder.significant_events'))
                                ->maxLength(2000),
                        ]),
                ]),
            Section::make(__('beneficiary.section.detailed_evaluation.heading.beneficiary_needs'))
                ->relationship('multidisciplinaryEvaluation')
                ->maxWidth('3xl')
                ->compact()
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
                ->relationship('multidisciplinaryEvaluation')
                ->maxWidth('3xl')
                ->compact()
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
                    Textarea::make('workplace')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.workplace'))
                        ->placeholder(__('beneficiary.placeholder.need_description'))
                        ->maxLength(1000),
                ]),
            Section::make(__('beneficiary.section.detailed_evaluation.heading.risk'))
                ->relationship('multidisciplinaryEvaluation')
                ->maxWidth('3xl')
                ->compact()
                ->schema([
                    Textarea::make('risk')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.risk'))
                        ->placeholder(__('beneficiary.placeholder.crisis_risk'))
                        ->maxLength(2000),
                ]),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected function getResultsStepSchema(): array
    {
        return [
            Section::make(__('beneficiary.wizard.results.label'))
                ->relationship('detailedEvaluationResult')
                ->maxWidth('3xl')
                ->schema([
                    CheckboxList::make('recommendation_services')
                        ->label(__('beneficiary.section.detailed_evaluation.heading.recommendation_services'))
                        ->options(RecommendationService::options())
                        ->columns(2),
                    Textarea::make('other_services_description')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.other_services'))
                        ->placeholder(__('beneficiary.placeholder.other_services'))
                        ->maxLength(100),
                    RichEditor::make('recommendations_for_intervention_plan')
                        ->label(__('beneficiary.section.detailed_evaluation.labels.recommendations_for_intervention_plan'))
                        ->placeholder(__('beneficiary.placeholder.recommendations_for_intervention_plan'))
                        ->maxLength(5000),
                ]),
        ];
    }
}
