<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\Pages;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\InitialEvaluation\InitialEvaluationResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditInitialEvaluation extends EditRecord
{
    protected static string $resource = InitialEvaluationResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $parent = $this->getParentRecord();
        if ($parent instanceof Model) {
            $this->redirect(CaseResource::getUrl('edit_initial_evaluation', ['record' => $parent]));
        }
    }
}
