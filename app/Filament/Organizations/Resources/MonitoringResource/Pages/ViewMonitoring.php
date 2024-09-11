<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiaryIdentity;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Infolists\Components\Actions\Edit;
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
        return __('beneficiary.section.monitoring.titles.view', ['file_number' => $this->getRecord()->number]);
    }

    protected function getHeaderActions(): array
    {
        // modal cancel action label is fix in pr #105
        return [
            Actions\DeleteAction::make()
                ->label(__('beneficiary.section.monitoring.actions.delete'))
                ->outlined()
                ->icon('heroicon-o-trash')
                ->modalHeading(__('beneficiary.section.monitoring.headings.modal_delete'))
                ->modalDescription(__('beneficiary.section.monitoring.labels.modal_delete_description'))
                ->modalSubmitActionLabel(__('beneficiary.section.monitoring.actions.delete'))
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
                    Tabs\Tab::make(__('beneficiary.section.monitoring.headings.details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.monitoring.headings.details'))
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
                                        ->label(__('beneficiary.section.monitoring.labels.date')),

                                    TextEntry::make('number')
                                        ->label(__('beneficiary.section.monitoring.labels.number')),

                                    TextEntry::make('start_date')
                                        ->label(__('beneficiary.section.monitoring.labels.start_date')),

                                    TextEntry::make('end_date')
                                        ->label(__('beneficiary.section.monitoring.labels.end_date')),

                                    TextEntry::make('specialists')
                                        ->label(__('beneficiary.section.monitoring.labels.team'))
                                        ->listWithLineBreaks()
                                        ->formatStateUsing(
                                            fn ($state) => $state->user->getFilamentName() . ' (' .
                                                $state->roles->map(fn ($item) => $item->label())->join(', ') . ')'
                                        ),
                                ]),
                        ]),

                    Tabs\Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                        ->maxWidth('3xl')
                        ->schema([
                            Group::make()
                                ->relationship('beneficiary')
                                ->schema(ViewBeneficiaryIdentity::identitySchemaForOtherPage($this->parent))]),

                    Tabs\Tab::make(__('beneficiary.section.monitoring.headings.child_info'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.monitoring.headings.child_info'))
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
                                                ->label(__('beneficiary.section.monitoring.labels.child_name'))
                                                ->columnSpanFull(),

                                            Grid::make()
                                                ->schema([
                                                    TextEntry::make('status')
                                                        ->label(__('beneficiary.section.monitoring.labels.status')),

                                                    TextEntry::make('age')
                                                        ->label(__('beneficiary.section.monitoring.labels.age')),

                                                    TextEntry::make('birthdate')
                                                        ->label(__('beneficiary.section.monitoring.labels.birthdate')),

                                                    TextEntry::make('aggressor_relationship')
                                                        ->label(__('beneficiary.section.monitoring.labels.aggressor_relationship')),

                                                    TextEntry::make('maintenance_sources')
                                                        ->label(__('beneficiary.section.monitoring.labels.maintenance_sources')),

                                                    TextEntry::make('location')
                                                        ->label(__('beneficiary.section.monitoring.labels.location')),

                                                    TextEntry::make('observations')
                                                        ->label(__('beneficiary.section.monitoring.labels.observations'))
                                                        ->columnSpanFull(),
                                                ]),
                                        ]),
                                ]),
                        ]),

                    Tabs\Tab::make(__('beneficiary.section.monitoring.headings.general'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('beneficiary.section.monitoring.headings.general'))
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
                                                ->label(__('beneficiary.section.monitoring.labels.admittance_date')),

                                            TextEntry::make('admittance_disposition')
                                                ->label(__('beneficiary.section.monitoring.labels.admittance_disposition')),
                                        ]),

                                    TextEntry::make('services_in_center')
                                        ->label(__('beneficiary.section.monitoring.labels.services_in_center')),

                                    ...$this->getGeneralMonitoringDataFields(),

                                    TextEntry::make('progress_placeholder')
                                        ->hiddenLabel()
                                        ->default(__('beneficiary.section.monitoring.headings.progress'))
                                        ->size(TextEntry\TextEntrySize::Medium),

                                    TextEntry::make('progress')
                                        ->label(__('beneficiary.section.monitoring.labels.progress')),

                                    TextEntry::make('progress_placeholder')
                                        ->hiddenLabel()
                                        ->default(__('beneficiary.section.monitoring.headings.observation'))
                                        ->size(TextEntry\TextEntrySize::Medium),

                                    TextEntry::make('observation')
                                        ->label(__('beneficiary.section.monitoring.labels.observation')),

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
                ->default(__(sprintf('beneficiary.section.monitoring.headings.%s', $field)))
                ->size(TextEntry\TextEntrySize::Medium);

            $formFields[] = TextEntry::make($field . '.objection')
                ->label(__('beneficiary.section.monitoring.labels.objection'));

            $formFields[] = TextEntry::make($field . '.activity')
                ->label(__('beneficiary.section.monitoring.labels.activity'));

            $formFields[] = TextEntry::make($field . '.conclusion')
                ->label(__('beneficiary.section.monitoring.labels.conclusion'));
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
