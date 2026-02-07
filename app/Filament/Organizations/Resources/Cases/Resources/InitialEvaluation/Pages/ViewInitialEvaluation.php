<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\InitialEvaluationResource;
use App\Filament\Organizations\Schemas\BeneficiaryResource\InitialEvaluationSchema;
use App\Models\Beneficiary;
use App\Models\EvaluateDetails;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewInitialEvaluation extends ViewRecord
{
    protected static string $resource = InitialEvaluationResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('beneficiary.page.initial_evaluation.title');
    }

    protected function getHeaderActions(): array
    {
        $parent = $this->getParentRecord();

        return [
            BackAction::make()
                ->url($parent instanceof Beneficiary
                    ? CaseResource::getUrl('view', ['record' => $parent])
                    : CaseResource::getUrl('index')),
            EditAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();
        $record = $this->getRecord();

        $breadcrumbs = [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
        ];
        if ($parent instanceof Beneficiary) {
            $breadcrumbs[CaseResource::getUrl('view', ['record' => $parent])] = $parent->getBreadcrumb();
        }
        $breadcrumbs[''] = $record instanceof EvaluateDetails
            ? __('beneficiary.page.initial_evaluation.title')
            : '';

        return $breadcrumbs;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema(InitialEvaluationSchema::getEvaluationDetailsInfolistComponents()),
                Section::make(__('beneficiary.wizard.violence.label'))
                    ->schema(InitialEvaluationSchema::getViolenceInfolistComponents()),
                Section::make(__('beneficiary.wizard.beneficiary_situation.label'))
                    ->schema(InitialEvaluationSchema::getBeneficiarySituationInfolistComponents()),
            ]);
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        $record = $this->getRecord();
        $beneficiary = $record instanceof EvaluateDetails ? $record->beneficiary : $record;

        return parent::defaultInfolist($schema)->record($beneficiary ?? $record);
    }
}
