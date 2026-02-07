<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\Pages;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\InitialEvaluationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateInitialEvaluation extends CreateRecord
{
    protected static string $resource = InitialEvaluationResource::class;

    public function mount(): void
    {
        $parent = $this->getParentRecord();
        if (! $parent instanceof Model) {
            $this->redirect(CaseResource::getUrl('index'));

            return;
        }
        if ($parent->evaluateDetails()->exists()) {
            $this->redirect(InitialEvaluationResource::getUrl('edit', [
                'beneficiary' => $parent,
                'record' => $parent->evaluateDetails,
            ]));

            return;
        }

        $this->redirect(CaseResource::getUrl('create_initial_evaluation', ['record' => $parent]));
    }
}
