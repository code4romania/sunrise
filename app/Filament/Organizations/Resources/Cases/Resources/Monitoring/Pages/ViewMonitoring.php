<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\Monitoring\Pages;

use App\Actions\BackAction;
use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\Monitoring\MonitoringResource;
use App\Infolists\Components\SectionHeader;
use App\Models\Beneficiary;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ViewMonitoring extends ViewRecord
{
    protected static string $resource = MonitoringResource::class;

    public function getTitle(): string|Htmlable
    {
        $monitoring = $this->getRecord();

        return __('monitoring.titles.view', ['file_number' => $monitoring->number ?? $monitoring->id]);
    }

    public function getBreadcrumbs(): array
    {
        $parent = $this->getParentRecord();
        $monitoring = $this->getRecord();

        return [
            CaseResource::getUrl('index') => __('case.view.breadcrumb_all'),
            CaseResource::getUrl('view', ['record' => $parent]) => $parent instanceof Beneficiary ? $parent->getBreadcrumb() : '',
            CaseResource::getUrl('edit_case_monitoring', ['record' => $parent]) => __('monitoring.titles.list'),
            '' => __('monitoring.breadcrumbs.file', ['file_number' => $monitoring->number ?? (string) $monitoring->id]),
        ];
    }

    protected function getHeaderActions(): array
    {
        $parent = $this->getParentRecord();

        return [
            BackAction::make()
                ->url(CaseResource::getUrl('edit_case_monitoring', ['record' => $parent])),
            DeleteAction::make()
                ->label(__('monitoring.actions.delete'))
                ->modalHeading(__('monitoring.headings.modal_delete'))
                ->modalDescription(__('monitoring.labels.modal_delete_description'))
                ->record($this->getRecord())
                ->successRedirectUrl(CaseResource::getUrl('edit_case_monitoring', ['record' => $parent]))
                ->outlined(),
        ];
    }

    public function defaultInfolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->inlineLabel(true)
            ->record($this->getRecord());
    }

    public function infolist(Schema $schema): Schema
    {
        $monitoring = $this->getRecord();

        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([
                    Tab::make(__('monitoring.headings.details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.details'))
                                ->schema([
                                    TextEntry::make('date')
                                        ->label(__('monitoring.labels.date'))
                                        ->formatStateUsing(fn ($state) => $this->formatDateSafe($state)),
                                    TextEntry::make('number')
                                        ->label(__('monitoring.labels.number')),
                                    TextEntry::make('start_date')
                                        ->label(__('monitoring.labels.start_date'))
                                        ->formatStateUsing(fn ($state) => $this->formatDateSafe($state)),
                                    TextEntry::make('end_date')
                                        ->label(__('monitoring.labels.end_date'))
                                        ->formatStateUsing(fn ($state) => $this->formatDateSafe($state)),
                                    TextEntry::make('interval')
                                        ->label(__('monitoring.headings.interval')),
                                    TextEntry::make('specialistsTeam.name_role')
                                        ->label(__('monitoring.labels.team'))
                                        ->listWithLineBreaks(),
                                ]),
                        ]),

                    Tab::make(__('monitoring.headings.child_info'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.child_info'))
                                ->visible(fn (): bool => $monitoring->children->isNotEmpty())
                                ->schema([
                                    RepeatableEntry::make('children')
                                        ->hiddenLabel()
                                        ->schema([
                                            TextEntry::make('name')
                                                ->label(__('monitoring.labels.child_name'))
                                                ->columnSpanFull(),
                                            Grid::make()
                                                ->schema([
                                                    TextEntry::make('status')
                                                        ->label(__('monitoring.labels.status')),
                                                    TextEntry::make('age')
                                                        ->label(__('monitoring.labels.age')),
                                                    TextEntry::make('birthdate')
                                                        ->label(__('monitoring.labels.birthdate'))
                                                        ->formatStateUsing(fn ($state) => $this->formatDateSafe($state)),
                                                    TextEntry::make('aggressor_relationship')
                                                        ->label(__('monitoring.labels.aggressor_relationship'))
                                                        ->formatStateUsing(fn ($state) => $state?->getLabel() ?? '—'),
                                                    TextEntry::make('maintenance_sources')
                                                        ->label(__('monitoring.labels.maintenance_sources'))
                                                        ->formatStateUsing(fn ($state) => $state?->getLabel() ?? '—'),
                                                    TextEntry::make('location')
                                                        ->label(__('monitoring.labels.location')),
                                                    TextEntry::make('observations')
                                                        ->label(__('monitoring.labels.observations'))
                                                        ->columnSpanFull(),
                                                ]),
                                        ]),
                                ]),
                            Section::make()
                                ->visible(fn (): bool => $monitoring->children->isEmpty())
                                ->schema([
                                    SectionHeader::make('empty_children')
                                        ->state(__('monitoring.headings.empty_state_children')),
                                ]),
                        ]),

                    Tab::make(__('monitoring.headings.general'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.general'))
                                ->schema([
                                    TextEntry::make('admittance_date')
                                        ->label(__('monitoring.labels.admittance_date'))
                                        ->formatStateUsing(fn ($state) => $this->formatDateSafe($state)),
                                    TextEntry::make('admittance_disposition')
                                        ->label(__('monitoring.labels.admittance_disposition')),
                                    TextEntry::make('services_in_center')
                                        ->label(__('monitoring.labels.services_in_center'))
                                        ->columnSpanFull(),
                                    TextEntry::make('progress')
                                        ->label(__('monitoring.labels.progress'))
                                        ->columnSpanFull(),
                                    TextEntry::make('observation')
                                        ->label(__('monitoring.labels.observation'))
                                        ->columnSpanFull(),
                                    ...$this->getGeneralMonitoringEntries(),
                                ]),
                        ]),
                ]),
        ]);
    }

    /**
     * @return array<int, TextEntry|\App\Infolists\Components\SectionHeader>
     */
    private function getGeneralMonitoringEntries(): array
    {
        $fields = [
            'protection_measures',
            'health_measures',
            'legal_measures',
            'psychological_measures',
            'aggressor_relationship',
            'others',
        ];
        $out = [];

        foreach ($fields as $field) {
            $out[] = SectionHeader::make($field.'_heading')
                ->state(__('monitoring.headings.'.$field));
            $out[] = TextEntry::make($field.'.objection')
                ->label(__('monitoring.labels.objection'));
            $out[] = TextEntry::make($field.'.activity')
                ->label(__('monitoring.labels.activity'));
            $out[] = TextEntry::make($field.'.conclusion')
                ->label(__('monitoring.labels.conclusion'));
        }

        return $out;
    }

    protected function hasInfolist(): bool
    {
        return true;
    }

    private function formatDateSafe(mixed $state): string
    {
        if ($state === null || $state === '' || $state === '-') {
            return '—';
        }
        try {
            return Carbon::parse($state)->format('d.m.Y');
        } catch (\Throwable) {
            return '—';
        }
    }
}
