<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\DetailedEvaluation;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Enums\RecommendationService;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditCaseDetailedEvaluationResults extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = CaseResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.wizard.results.label');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record->getBreadcrumb(),
            CaseResource::getUrl('view_detailed_evaluation', ['record' => $record]) => __('beneficiary.breadcrumb.wizard_detailed_evaluation'),
            '' => __('beneficiary.wizard.results.label'),
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
        return CaseResource::getUrl('view_detailed_evaluation', ['record' => $this->getRecord()]).'?tab='.\Illuminate\Support\Str::slug(__('beneficiary.wizard.results.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
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
            ]);
    }
}
