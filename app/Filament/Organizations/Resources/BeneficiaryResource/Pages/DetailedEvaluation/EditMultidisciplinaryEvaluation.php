<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Pages\DetailedEvaluation;

use App\Concerns\RedirectToDetailedEvaluation;
use App\Enums\Applicant;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use Livewire\Component;

class EditMultidisciplinaryEvaluation extends EditRecord
{
    use RedirectToDetailedEvaluation;

    protected static string $resource = BeneficiaryResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.edit_multidisciplinary_evaluation.title');
    }

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->getRecord())
            ->getBreadcrumbs('view_detailed_evaluation');
    }

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    protected function getTabSlug(): string
    {
        return Str::slug(__('beneficiary.wizard.multidisciplinary_evaluation.label'));
    }

    public static function getSchema(): array
    {
        return [
            Section::make()
                ->maxWidth('3xl')
                ->schema([
                    Section::make(__('beneficiary.section.detailed_evaluation.heading.reasons_for_start_evaluation'))
                        ->relationship('multidisciplinaryEvaluation')
                        ->columns()
                        ->compact()
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
                        ]),

                    Section::make(__('beneficiary.section.detailed_evaluation.heading.historic_violence'))
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

                    Group::make()
                        ->relationship('multidisciplinaryEvaluation')
                        ->schema([
                            Section::make(__('beneficiary.section.detailed_evaluation.heading.beneficiary_needs'))
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
                                ]),

                            Section::make(__('beneficiary.section.detailed_evaluation.heading.risk'))
                                ->compact()
                                ->schema([
                                    Textarea::make('risk')
                                        ->label(__('beneficiary.section.detailed_evaluation.labels.risk'))
                                        ->placeholder(__('beneficiary.placeholder.crisis_risk'))
                                        ->maxLength(2000),
                                ]),
                        ]),
                ]),
        ];
    }
}
