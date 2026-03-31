<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Enums\Applicant;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditCaseDetailedEvaluationMultidisciplinary extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.wizard.multidisciplinary_evaluation.label');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record->getBreadcrumb(),
            CaseResource::getUrl('view_detailed_evaluation', ['record' => $record]) => __('beneficiary.breadcrumb.wizard_detailed_evaluation'),
            '' => __('beneficiary.wizard.multidisciplinary_evaluation.label'),
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
        return CaseResource::getUrl('view_detailed_evaluation', ['record' => $this->getRecord()]).'?tab='.\Illuminate\Support\Str::slug(__('beneficiary.wizard.multidisciplinary_evaluation.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
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
            ]);
    }
}
