<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Filament\Organizations\Concerns\InteractsWithBeneficiaryDetailsPanel;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\SectionHeader;
use App\Models\Beneficiary;
use App\Models\MonthlyPlan;
use App\Models\Specialist;
use App\Services\CaseExports\CaseExportManager;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewCaseMonthlyPlan extends ViewRecord
{
    use InteractsWithBeneficiaryDetailsPanel;

    protected static string $resource = CaseResource::class;

    /**
     * Monthly plan primary key from the URL. Must be public so Livewire keeps it
     * across requests; {@see request()->route('monthlyPlan')} is unavailable on
     * Livewire subrequests (e.g. header actions).
     */
    public null|int|string $monthlyPlan = null;

    private ?MonthlyPlan $resolvedMonthlyPlan = null;

    public function mount(int|string $record, MonthlyPlan|int|string|null $monthlyPlan = null): void
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

        $this->monthlyPlan = $this->normalizeMonthlyPlanRouteKey($monthlyPlan)
            ?? $this->normalizeMonthlyPlanRouteKey(request()->route('monthlyPlan'));

        if ($this->monthlyPlan === null || $this->monthlyPlan === '') {
            abort(404);
        }

        $this->resolveMonthlyPlan();

        $this->authorizeAccess();
    }

    private function normalizeMonthlyPlanRouteKey(mixed $value): int|string|null
    {
        if ($value instanceof MonthlyPlan) {
            return $value->getKey();
        }

        if ($value === null || $value === '') {
            return null;
        }

        return $value;
    }

    private function resolveMonthlyPlan(): MonthlyPlan
    {
        if ($this->resolvedMonthlyPlan instanceof MonthlyPlan) {
            return $this->resolvedMonthlyPlan;
        }

        $beneficiary = $this->getRecord();
        if (! $beneficiary instanceof Beneficiary) {
            abort(404);
        }

        $plan = $beneficiary->interventionPlan;
        if (! $plan) {
            abort(404);
        }

        $monthlyPlan = MonthlyPlan::query()
            ->where('intervention_plan_id', $plan->id)
            ->where('id', $this->monthlyPlan)
            ->with([
                'monthlyPlanServices.service',
                'monthlyPlanServices.monthlyPlanInterventions.serviceIntervention',
                'interventionPlan',
                'interventionPlan.beneficiary.effective_residence.county',
                'interventionPlan.beneficiary.effective_residence.city',
            ])
            ->firstOrFail();

        return $this->resolvedMonthlyPlan = $monthlyPlan;
    }

    protected function authorizeAccess(): void
    {
        abort_unless(CaseResource::canView($this->record), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.monthly_plan');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $record]) => $record instanceof Beneficiary ? $record->getBreadcrumb() : '',
            CaseResource::getUrl('view_intervention_plan', ['record' => $record]) => __('intervention_plan.headings.view_page'),
            '' => __('intervention_plan.headings.monthly_plan'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view_intervention_plan', ['record' => $this->getRecord()])),
            Action::make('download_monthly_sheet')
                ->label(__('intervention_plan.actions.download_monthly_sheet'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->outlined()
                ->action(fn (): StreamedResponse => app(CaseExportManager::class)->downloadMonthlyPlanSheetPdf(
                    $this->resolveMonthlyPlan(),
                )),
            DeleteAction::make()
                ->label(__('intervention_plan.actions.delete_monthly_plan'))
                ->modalHeading(__('intervention_plan.headings.delete_monthly_plan_modal'))
                ->modalDescription(__('intervention_plan.labels.delete_monthly_plan_modal_description'))
                ->modalSubmitActionLabel(__('intervention_plan.actions.delete_monthly_plan'))
                ->successRedirectUrl(CaseResource::getUrl('view_intervention_plan', ['record' => $this->getRecord()]))
                ->outlined(),
        ];
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->inlineLabel(true)
            ->record($this->resolveMonthlyPlan());
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([
                    Tab::make(__('intervention_plan.headings.monthly_plan_details'))
                        ->schema([
                            Section::make()
                                ->maxWidth('3xl')
                                ->columns(2)
                                ->schema([
                                    SectionHeader::make('monthly_plan_details')
                                        ->state(__('intervention_plan.headings.monthly_plan_details'))
                                        ->action(
                                            EditAction::make()
                                                ->url(fn (): string => CaseResource::getUrl('edit_monthly_plan_details', [
                                                    'record' => $this->getRecord(),
                                                    'monthlyPlan' => $this->resolveMonthlyPlan(),
                                                ]))
                                        ),

                                    TextEntry::make('beneficiary.full_name')
                                        ->label(__('intervention_plan.labels.beneficiary_full_name')),

                                    TextEntry::make('beneficiary.cnp')
                                        ->label(__('field.cnp')),

                                    TextEntry::make('domiciliu')
                                        ->label(__('intervention_plan.labels.domiciliu'))
                                        ->state(function (?MonthlyPlan $record): string {
                                            $plan = $record ?? $this->resolveMonthlyPlan();

                                            $beneficiary = $plan->beneficiary;
                                            if (! $beneficiary instanceof Beneficiary) {
                                                return '—';
                                            }

                                            return self::formatAddress($beneficiary);
                                        }),

                                    TextEntry::make('interventionPlan.admit_date_in_center')
                                        ->label(__('intervention_plan.labels.admit_date_in_center'))
                                        ->formatStateUsing(fn (mixed $state): string => self::formatDateState($state)),

                                    TextEntry::make('interventionPlan.plan_date')
                                        ->label(__('intervention_plan.labels.plan_date'))
                                        ->formatStateUsing(fn (mixed $state): string => self::formatDateState($state)),

                                    TextEntry::make('interventionPlan.last_revise_date')
                                        ->label(__('intervention_plan.labels.last_revise_date'))
                                        ->formatStateUsing(fn (mixed $state): string => self::formatDateState($state)),

                                    TextEntry::make('interval')
                                        ->label(__('intervention_plan.labels.interval')),

                                    TextEntry::make('caseManager.full_name')
                                        ->label(__('intervention_plan.labels.case_manager')),

                                    TextEntry::make('case_team_display')
                                        ->label(__('intervention_plan.labels.specialists'))
                                        ->state(function (?MonthlyPlan $record): string {
                                            $plan = $record ?? $this->resolveMonthlyPlan();

                                            return self::formatMonthlyPlanCaseTeam($plan);
                                        }),
                                ]),
                        ]),

                    Tab::make(__('intervention_plan.headings.services_and_interventions'))
                        ->schema([
                            Section::make()
                                ->columnSpanFull()
                                ->maxWidth('3xl')
                                ->schema([
                                    SectionHeader::make('services_header')
                                        ->state(__('intervention_plan.headings.services_and_interventions'))
                                        ->action(
                                            EditAction::make()
                                                ->url(fn (): string => CaseResource::getUrl('edit_monthly_plan_services', [
                                                    'record' => $this->getRecord(),
                                                    'monthlyPlan' => $this->resolveMonthlyPlan(),
                                                ]))
                                        ),

                                    View::make('filament.organizations.components.monthly-plan-services-and-interventions')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ]);
    }

    /**
     * @return list<int>
     */
    private static function monthlyPlanSpecialistIds(MonthlyPlan $record): array
    {
        $fromCast = $record->specialists;
        if ($fromCast instanceof Collection && $fromCast->isNotEmpty()) {
            return self::normalizeSpecialistIdList($fromCast->all());
        }

        $raw = $record->getRawOriginal('specialists');
        if ($raw === null || $raw === '') {
            return [];
        }

        if (! is_string($raw)) {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return self::normalizeSpecialistIdList($decoded);
        }

        return self::normalizeSpecialistIdList(array_map('trim', explode(',', $raw)));
    }

    /**
     * @param  array<mixed>  $values
     * @return list<int>
     */
    private static function normalizeSpecialistIdList(array $values): array
    {
        $ids = [];
        foreach ($values as $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $ids[] = (int) $value;
        }

        /** @var list<int> $unique */
        $unique = array_values(array_unique(array_filter($ids, static fn (int $id): bool => $id > 0)));

        return $unique;
    }

    private static function formatMonthlyPlanCaseTeam(MonthlyPlan $record): string
    {
        $ids = self::monthlyPlanSpecialistIds($record);
        if ($ids === []) {
            return '—';
        }

        $specialists = Specialist::query()
            ->whereIn('id', $ids)
            ->with(['user', 'roleForDisplay'])
            ->get();

        if ($specialists->isEmpty()) {
            return '—';
        }

        $labels = collect($ids)
            ->map(static function (int $id) use ($specialists): ?string {
                $specialist = $specialists->firstWhere('id', $id);

                return $specialist?->name_role;
            })
            ->filter()
            ->values()
            ->implode(', ');

        return $labels !== '' ? $labels : '—';
    }

    private static function formatDateState(mixed $state): string
    {
        if ($state === null || $state === '' || $state === '-') {
            return '—';
        }

        try {
            return Carbon::parse($state)->translatedFormat('d.m.Y');
        } catch (\Throwable) {
            return '—';
        }
    }

    private static function parseableDate(mixed $value): bool
    {
        if ($value === null || $value === '' || $value === '-') {
            return false;
        }

        try {
            Carbon::parse($value);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private static function formatAddress(Beneficiary $beneficiary): string
    {
        $addr = $beneficiary->effective_residence;
        if (! $addr) {
            return '';
        }
        $parts = array_filter([
            $addr->address,
            $addr->city?->name,
            $addr->county ? __('field.county').' '.$addr->county->name : null,
        ]);

        return implode(', ', $parts);
    }
}
