<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Models\Beneficiary;
use App\Models\InterventionService;
use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewCaseInterventionService extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public ?InterventionService $interventionService = null;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        if (! $this->record instanceof Beneficiary) {
            abort(404);
        }

        $plan = $this->record->interventionPlan;
        if (! $plan) {
            $this->redirect(CaseResource::getUrl('view_intervention_plan', ['record' => $this->record]));

            return;
        }

        $interventionServiceId = request()->route('interventionService');
        $this->interventionService = InterventionService::query()
            ->where('intervention_plan_id', $plan->id)
            ->where('id', $interventionServiceId)
            ->with([
                'organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition',
                'specialist.user',
                'specialist.role',
            ])
            ->firstOrFail();

        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        abort_unless(CaseResource::canView($this->record), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->interventionService?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? __('intervention_plan.headings.services');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            '' => $this->interventionService?->organizationServiceWithoutStatusCondition?->serviceWithoutStatusCondition?->name ?? __('intervention_plan.headings.services'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_intervention_plan', ['record' => $this->getRecord()])),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        $service = $this->interventionService;

        return $schema
            ->record($service)
            ->components([
                Section::make(__('intervention_plan.headings.services'))
                    ->schema([
                        TextEntry::make('organizationServiceWithoutStatusCondition.serviceWithoutStatusCondition.name')
                            ->label(__('intervention_plan.labels.service'))
                            ->placeholder('—'),
                        TextEntry::make('specialist.name_role')
                            ->label(__('intervention_plan.labels.specialist'))
                            ->placeholder('—'),
                        TextEntry::make('institution')
                            ->label(__('intervention_plan.labels.responsible_institution'))
                            ->placeholder('—'),
                        TextEntry::make('start_date_interval')
                            ->label(__('intervention_plan.labels.start_date_interval'))
                            ->formatStateUsing(fn ($state) => self::formatDate($state))
                            ->placeholder('—'),
                        TextEntry::make('end_date_interval')
                            ->label(__('intervention_plan.labels.end_date_interval'))
                            ->formatStateUsing(fn ($state) => self::formatDate($state))
                            ->placeholder('—'),
                        TextEntry::make('interventions_count')
                            ->label(__('intervention_plan.labels.interventions_count'))
                            ->state((string) $service->beneficiaryInterventions()->count())
                            ->placeholder('0'),
                        TextEntry::make('meetings_count')
                            ->label(__('intervention_plan.labels.meetings_count'))
                            ->state((string) $service->meetings()->count())
                            ->placeholder('0'),
                    ])
                    ->columns(2),
            ]);
    }

    private static function formatDate(?string $state): string
    {
        if ($state === null || $state === '') {
            return '—';
        }

        try {
            return Carbon::parse($state)->translatedFormat('d.m.Y');
        } catch (\Throwable) {
            return '—';
        }
    }
}
