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
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

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
                ->modalHeading(__('intervention_plan.headings.edit_intervention_plan_modal'))
                ->fillForm(fn (): array => $record->interventionPlan->only([
                    'admit_date_in_center',
                    'plan_date',
                    'last_revise_date',
                ]))
                ->schema([
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
            Action::make('download_plan')
                ->label(__('intervention_plan.actions.download_plan'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->outlined()
                ->action(function (): void {
                    Notification::make()
                        ->info()
                        ->title(__('intervention_plan.actions.download_plan'))
                        ->body(__('intervention_plan.labels.download_plan_coming_soon'))
                        ->send();
                }),
            Action::make('beneficiary_details')
                ->record($record)
                ->slideOver()
                ->modalHeading(__('case.view.identity_page.fab_beneficiary_details'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('general.action.close'))
                ->schema([
                    TextEntry::make('full_name')
                        ->label('Nume și prenume')
                        ->placeholder('—'),
                    TextEntry::make('created_at')
                        ->label('Data creării cazului')
                        ->formatStateUsing(fn (mixed $state): string => self::formatDateState($state))
                        ->placeholder('—'),
                    TextEntry::make('age')
                        ->label(__('field.age'))
                        ->state(fn (Beneficiary $record): string => $record->age !== null ? (string) $record->age : '—'),
                    TextEntry::make('civil_status')
                        ->label(__('field.civil_status'))
                        ->state(fn (Beneficiary $record): string => self::formatEnumLabel($record->civil_status)),
                    TextEntry::make('children_total_count')
                        ->label('Număr total copii')
                        ->placeholder('—'),
                    TextEntry::make('children_under_18_care_count')
                        ->label('Număr copii în întreținere cu vârsta < 18 ani')
                        ->placeholder('—'),
                    TextEntry::make('legal_residence_city')
                        ->label('Oraș/UAT domiciliu legal')
                        ->state(fn (Beneficiary $record): string => $record->legal_residence?->city?->name ?? '—'),
                    TextEntry::make('effective_residence_city')
                        ->label('Localitate domiciliu efectiv')
                        ->state(fn (Beneficiary $record): string => $record->effective_residence?->city?->name ?? '—'),
                    TextEntry::make('details.studies')
                        ->label(__('field.studies'))
                        ->state(fn (Beneficiary $record): string => self::formatEnumLabel($record->details?->studies)),
                    TextEntry::make('details.occupation')
                        ->label(__('field.occupation'))
                        ->state(fn (Beneficiary $record): string => self::formatEnumLabel($record->details?->occupation)),
                    TextEntry::make('details.net_income')
                        ->label('Venit lunar net (din toate sursele)')
                        ->state(function (Beneficiary $record): string {
                            $income = $record->details?->net_income;

                            return blank($income) ? '—' : "{$income} RON";
                        }),
                    TextEntry::make('details.homeownership')
                        ->label('Dreptul de proprietate asupra locuinței primare')
                        ->state(fn (Beneficiary $record): string => self::formatEnumLabel($record->details?->homeownership)),
                    TextEntry::make('aggressor_relationship')
                        ->label('Relația victimei cu agresorul')
                        ->state(fn (Beneficiary $record): string => self::formatEnumLabel($record->aggressors->first()?->relationship)),
                    TextEntry::make('aggressor_legal_history')
                        ->label('Aspecte legale agresor')
                        ->state(function (Beneficiary $record): string {
                            $values = $record->aggressors->first()?->legal_history;

                            return self::formatCollectionLabels($values);
                        }),
                    TextEntry::make('flowPresentation.presentation_mode')
                        ->label('Modalitatea de prezentare')
                        ->state(fn (Beneficiary $record): string => self::formatEnumLabel($record->flowPresentation?->presentation_mode)),
                    TextEntry::make('flowPresentation.act_location')
                        ->label('Locul producerii actelor de VD')
                        ->state(fn (Beneficiary $record): string => self::formatCollectionLabels($record->flowPresentation?->act_location)),
                ])
                ->extraModalFooterActions([
                    Action::make('view_full_beneficiary')
                        ->label(__('case.view.view_full_beneficiary'))
                        ->url(fn (): string => CaseResource::getUrl('view', ['record' => $record]))
                        ->button()
                        ->close(),
                ]),
        ];
    }

    public function openBeneficiaryDetailsSlideOver(): void
    {
        $this->mountAction('beneficiary_details');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString('intervention-plan-tab')
                ->tabs([
                    Tab::make(__('intervention_plan.headings.plan_intervention_details'))
                        ->schema([
                            Section::make()
                                ->columns(3)
                                ->schema([
                                    TextEntry::make('full_name')
                                        ->label(__('intervention_plan.labels.beneficiary_full_name')),
                                    TextEntry::make('cnp')
                                        ->label(__('intervention_plan.labels.cnp')),
                                    TextEntry::make('address')
                                        ->label(__('intervention_plan.labels.domiciliu'))
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
                        ]),
                    Tab::make(__('intervention_plan.headings.social_services'))
                        ->schema($this->getWidgetsSchemaComponents([InterventionPlanServicesWidget::class])),
                    Tab::make(__('intervention_plan.headings.benefit_services'))
                        ->schema($this->getWidgetsSchemaComponents([InterventionPlanBenefitsWidget::class])),
                    Tab::make(__('intervention_plan.headings.results_centralizer'))
                        ->schema($this->getWidgetsSchemaComponents([InterventionPlanResultsWidget::class])),
                    Tab::make(__('intervention_plan.headings.monthly_plan'))
                        ->schema($this->getWidgetsSchemaComponents([InterventionPlanMonthlyPlansWidget::class])),
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
            $addr->county ? __('field.county') . ' ' . $addr->county->name : null,
        ]);

        return implode(', ', $parts);
    }

    private static function formatEnumLabel(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return \is_object($value) && method_exists($value, 'getLabel')
            ? (string) $value->getLabel()
            : (string) $value;
    }

    private static function formatCollectionLabels(mixed $values): string
    {
        if ($values instanceof Collection || $values instanceof Arrayable || \is_array($values)) {
            $items = collect($values)
                ->map(fn (mixed $item): string => self::formatEnumLabel($item))
                ->filter(fn (string $item): bool => $item !== '—')
                ->values();

            return $items->isNotEmpty() ? $items->implode('; ') : '—';
        }

        return self::formatEnumLabel($values);
    }

    /**
     * @return array<int, class-string<\Filament\Widgets\Widget>>
     */
    protected function getFooterWidgets(): array
    {
        return [];
    }
}
