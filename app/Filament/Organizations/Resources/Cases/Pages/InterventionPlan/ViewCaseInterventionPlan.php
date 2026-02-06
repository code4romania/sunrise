<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets\InterventionPlanBenefitsWidget;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets\InterventionPlanMonthlyPlansWidget;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets\InterventionPlanResultsWidget;
use App\Filament\Organizations\Resources\Cases\Pages\InterventionPlan\Widgets\InterventionPlanServicesWidget;
use App\Forms\Components\DatePicker;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ViewCaseInterventionPlan extends ViewRecord
{
    protected static string $resource = CaseResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if ($this->getRecord()->interventionPlan === null) {
            $this->redirect(CaseResource::getUrl('view', ['record' => $this->getRecord()]), navigate: true);
        }
    }

    public function getTitle(): string|Htmlable
    {
        return __('intervention_plan.headings.view_page');
    }

    public function getBreadcrumbs(): array
    {
        $record = $this->getRecord();

        $breadcrumbs = [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
        ];

        if ($record instanceof Beneficiary) {
            $breadcrumbs[CaseResource::getUrl('view', ['record' => $record])] = $record->getBreadcrumb();
        }

        $breadcrumbs[''] = __('intervention_plan.headings.view_page');

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('view', ['record' => $record])),
            EditAction::make()
                ->label(__('intervention_plan.actions.edit_intervention_plan'))
                ->icon(Heroicon::OutlinedPencilSquare)
                ->outlined()
                ->modalHeading(__('intervention_plan.headings.edit_intervention_plan'))
                ->fillForm(fn (): array => $record->interventionPlan->only([
                    'admit_date_in_center',
                    'plan_date',
                    'last_revise_date',
                ]))
                ->form([
                    Grid::make(1)
                        ->schema([
                            Placeholder::make('beneficiary_name')
                                ->label(__('intervention_plan.labels.beneficiary_name'))
                                ->content(fn (): string => $record->full_name),
                            Placeholder::make('beneficiary_cnp')
                                ->label(__('intervention_plan.labels.cnp'))
                                ->content(fn (): string => $record->cnp ?? '—'),
                            Placeholder::make('beneficiary_address')
                                ->label(__('intervention_plan.labels.address'))
                                ->content(fn (): string => self::formatAddress($record)),
                        ]),
                    Grid::make(1)
                        ->schema([
                            DatePicker::make('admit_date_in_center')
                                ->label(__('intervention_plan.labels.admit_date_in_center')),
                            DatePicker::make('plan_date')
                                ->label(__('intervention_plan.labels.plan_date')),
                            DatePicker::make('last_revise_date')
                                ->label(__('intervention_plan.labels.last_revise_date')),
                        ]),
                ])
                ->action(function (array $data) use ($record): void {
                    $record->interventionPlan->update($data);
                    $record->unsetRelation('interventionPlan');
                    Notification::make()
                        ->success()
                        ->title(__('filament-actions::edit.single.notifications.saved.title'))
                        ->send();
                }),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('intervention_plan.headings.plan_details'))
                ->columns(3)
                ->schema([
                    TextEntry::make('full_name')
                        ->label(__('intervention_plan.labels.full_name')),
                    TextEntry::make('cnp')
                        ->label(__('intervention_plan.labels.cnp')),
                    TextEntry::make('address')
                        ->label(__('intervention_plan.labels.address'))
                        ->state(fn (Beneficiary $record): string => self::formatAddress($record)),
                    TextEntry::make('interventionPlan.admit_date_in_center')
                        ->label(__('intervention_plan.labels.admit_date_in_center'))
                        ->formatStateUsing(fn (mixed $state): string => self::formatDateState($state)),
                    TextEntry::make('interventionPlan.plan_date')
                        ->label(__('intervention_plan.labels.plan_date'))
                        ->formatStateUsing(fn (mixed $state): string => self::formatDateState($state)),
                    TextEntry::make('interventionPlan.last_revise_date')
                        ->label(__('intervention_plan.labels.last_revise_date'))
                        ->formatStateUsing(fn (mixed $state): string => self::formatDateState($state)),
                ]),
        ]);
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

    private static function formatAddress(Beneficiary $record): string
    {
        $addr = $record->effective_residence;
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

    /**
     * @return array<int, class-string<\Filament\Widgets\Widget>>
     */
    protected function getFooterWidgets(): array
    {
        return [
            InterventionPlanServicesWidget::class,
            InterventionPlanBenefitsWidget::class,
            InterventionPlanResultsWidget::class,
            InterventionPlanMonthlyPlansWidget::class,
        ];
    }
}
