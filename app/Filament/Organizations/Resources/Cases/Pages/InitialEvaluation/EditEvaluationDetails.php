<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InitialEvaluation;

use App\Actions\BackAction;
use App\Concerns\PreventSubmitFormOnEnter;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\InitialEvaluationResource;
use App\Filament\Organizations\Schemas\BeneficiaryResource\InitialEvaluationSchema;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class EditEvaluationDetails extends EditRecord
{
    use PreventSubmitFormOnEnter;

    protected static string $resource = CaseResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->getRecord()->evaluateDetails === null) {
            $this->redirect(CaseResource::getUrl('view', ['record' => $this->getRecord()]));
        }
    }

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
            InitialEvaluationResource::getUrl('view', [
                'beneficiary' => $record,
                'record' => $record->evaluateDetails,
            ]) => __('beneficiary.page.initial_evaluation.title'),
            '' => __('beneficiary.wizard.details.label'),
        ];
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url(InitialEvaluationResource::getUrl('view', [
                    'beneficiary' => $record,
                    'record' => $record->evaluateDetails,
                ])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();

        return InitialEvaluationResource::getUrl('view', [
            'beneficiary' => $record,
            'record' => $record->evaluateDetails,
        ]).'?tab='.\Illuminate\Support\Str::slug(__('beneficiary.wizard.details.label'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components(InitialEvaluationSchema::getEvaluationDetailsFormComponents());
    }
}
