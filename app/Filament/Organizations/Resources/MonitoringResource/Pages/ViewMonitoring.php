<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiaryIdentity;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Infolists\Components\Actions\Edit;
use App\Infolists\Components\SectionHeader;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Support\Htmlable;

class ViewMonitoring extends ViewRecord
{
    use HasParentResource;

    protected static string $resource = MonitoringResource::class;

    public function getBreadcrumbs(): array
    {
        return BeneficiaryBreadcrumb::make($this->parent)->getBreadcrumbsForMonitoringFile($this->getRecord());
    }

    public function getTitle(): string|Htmlable
    {
        return __('monitoring.titles.view', ['file_number' => $this->getRecord()->number]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('monitoring.actions.delete'))
                ->outlined()
                ->icon('heroicon-o-trash')
                ->modalHeading(__('monitoring.headings.modal_delete'))
                ->modalDescription(__('monitoring.labels.modal_delete_description'))
                ->modalSubmitActionLabel(__('monitoring.actions.delete'))
                ->modalIcon()
                ->modalAlignment(Alignment::Left),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([
                    Tabs\Tab::make(__('monitoring.headings.details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.details'))
                                ->headerActions([
                                    Edit::make('edit_details')
                                        ->url(self::getParentResource()::getUrl('monitoring.edit_details', [
                                            'parent' => $this->parent,
                                            'record' => $this->getRecord(),
                                        ])),
                                ])
                                ->columns()
                                ->schema([
                                    TextEntry::make('date')
                                        ->label(__('monitoring.labels.date')),

                                    TextEntry::make('number')
                                        ->label(__('monitoring.labels.number')),

                                    TextEntry::make('start_date')
                                        ->label(__('monitoring.labels.start_date')),

                                    TextEntry::make('end_date')
                                        ->label(__('monitoring.labels.end_date')),

                                    TextEntry::make('specialistsTeam.name_role')
                                        ->label(__('monitoring.labels.team'))
                                        ->listWithLineBreaks(),
                                ]),
                        ]),

                    Tabs\Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                        ->maxWidth('3xl')
                        ->schema([
                            Group::make()
                                ->relationship('beneficiary')
                                ->schema(ViewBeneficiaryIdentity::identitySchemaForOtherPage($this->parent))]),

                    Tabs\Tab::make(__('monitoring.headings.child_info'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.child_info'))
                                ->visible(fn () => $this->getRecord()->children->isNotEmpty())
                                ->headerActions([
                                    Edit::make('edit_details')
                                        ->url(self::getParentResource()::getUrl('monitoring.edit_children', [
                                            'parent' => $this->parent,
                                            'record' => $this->getRecord(),
                                        ])),
                                ])
                                ->schema([
                                    RepeatableEntry::make('children')
                                        ->hiddenLabel()
                                        ->maxWidth('3xl')
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
                                                        ->formatStateUsing(fn ($state) => $state !== '-' ? $state->format('d-m-Y') : $state),

                                                    TextEntry::make('aggressor_relationship')
                                                        ->label(__('monitoring.labels.aggressor_relationship')),

                                                    TextEntry::make('maintenance_sources')
                                                        ->label(__('monitoring.labels.maintenance_sources')),

                                                    TextEntry::make('location')
                                                        ->label(__('monitoring.labels.location')),

                                                    TextEntry::make('observations')
                                                        ->label(__('monitoring.labels.observations'))
                                                        ->columnSpanFull(),
                                                ]),
                                        ]),
                                ]),

                            Section::make()
                                ->visible(fn () => $this->getRecord()->children->isEmpty())
                                ->schema([
                                    SectionHeader::make('empty_children')
                                        ->state(__('monitoring.headings.empty_state_children')),
                                ]),
                        ]),

                    Tabs\Tab::make(__('monitoring.headings.general'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.general'))
                                ->headerActions([
                                    Edit::make('edit_details')
                                        ->url(self::getParentResource()::getUrl('monitoring.edit_general', [
                                            'parent' => $this->parent,
                                            'record' => $this->getRecord(),
                                        ])),
                                ])
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            TextEntry::make('admittance_date')
                                                ->label(__('monitoring.labels.admittance_date')),

                                            TextEntry::make('admittance_disposition')
                                                ->label(__('monitoring.labels.admittance_disposition')),
                                        ]),

                                    TextEntry::make('services_in_center')
                                        ->label(__('monitoring.labels.services_in_center')),

                                    ...$this->getGeneralMonitoringDataFields(),

                                    TextEntry::make('progress_placeholder')
                                        ->hiddenLabel()
                                        ->default(__('monitoring.headings.progress'))
                                        ->size(TextEntry\TextEntrySize::Medium),

                                    TextEntry::make('progress')
                                        ->label(__('monitoring.labels.progress')),

                                    TextEntry::make('progress_placeholder')
                                        ->hiddenLabel()
                                        ->default(__('monitoring.headings.observation'))
                                        ->size(TextEntry\TextEntrySize::Medium),

                                    TextEntry::make('observation')
                                        ->label(__('monitoring.labels.observation')),

                                ]),
                        ]),

                ]),
        ]);
    }

    private function getGeneralMonitoringDataFields(): array
    {
        $formFields = [];
        $fields = [
            'protection_measures',
            'health_measures',
            'legal_measures',
            'psychological_measures',
            'aggressor_relationship',
            'others',
        ];

        foreach ($fields as $field) {
            $formFields[] = TextEntry::make('progress_placeholder')
                ->hiddenLabel()
                ->default(__(\sprintf('monitoring.headings.%s', $field)))
                ->size(TextEntry\TextEntrySize::Medium);

            $formFields[] = TextEntry::make($field . '.objection')
                ->label(__('monitoring.labels.objection'));

            $formFields[] = TextEntry::make($field . '.activity')
                ->label(__('monitoring.labels.activity'));

            $formFields[] = TextEntry::make($field . '.conclusion')
                ->label(__('monitoring.labels.conclusion'));
        }

        return $formFields;
    }

    protected function configureDeleteAction(Actions\DeleteAction $action): void
    {
        $resource = static::getResource();

        $action->authorize($resource::canDelete($this->getRecord()))
            ->successRedirectUrl(static::getParentResource()::getUrl('monitorings.index', [
                'parent' => $this->parent,
            ]));
    }
}
