<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use App\Models\InterventionPlan;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class CreateCaseInterventionPlan extends Page
{
    use InteractsWithRecord;

    protected static string $resource = CaseResource::class;

    protected string $view = 'filament.organizations.pages.intervention-plan.create';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if (! $this->record instanceof Beneficiary) {
            abort(404);
        }

        $plan = $this->record->interventionPlan;

        if ($plan === null) {
            $plan = InterventionPlan::create([
                'beneficiary_id' => $this->record->id,
                'admit_date_in_center' => $this->record->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'plan_date' => now()->format('Y-m-d'),
            ]);
        }

        $this->redirect(CaseResource::getUrl('view_intervention_plan', [
            'record' => $this->record,
        ]), navigate: true);
    }
}
