<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\MonitoringResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\TextSize;
use App\Actions\BackAction;
use App\Concerns\HasParentResource;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Filament\Organizations\Resources\BeneficiaryResource\Pages\ViewBeneficiaryIdentity;
use App\Filament\Organizations\Resources\MonitoringResource;
use App\Infolists\Components\Actions\EditAction;
use App\Infolists\Components\DateEntry;
use App\Infolists\Components\SectionHeader;
use App\Services\Breadcrumb\BeneficiaryBreadcrumb;
use Filament\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
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
            BackAction::make()
                ->url(BeneficiaryResource::getUrl('monitorings.index', ['parent' => $this->parent])),

            DeleteAction::make()
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

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([
                    Tab::make(__('monitoring.headings.details'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.details'))
                                ->headerActions([
                                    EditAction::make()
                                        ->url(self::getParentResource()::getUrl('monitoring.edit_details', [
                                            'parent' => $this->parent,
                                            'record' => $this->getRecord(),
                                        ])),
                                ])
                                ->columns()
                                ->schema([
                                    DateEntry::make('date')
                                        ->label(__('monitoring.labels.date')),

                                    TextEntry::make('number')
                                        ->label(__('monitoring.labels.number')),

                                    DateEntry::make('start_date')
                                        ->label(__('monitoring.labels.start_date')),

                                    DateEntry::make('end_date')
                                        ->label(__('monitoring.labels.end_date')),

                                    TextEntry::make('specialistsTeam.name_role')
                                        ->label(__('monitoring.labels.team'))
                                        ->listWithLineBreaks(),
                                ]),
                        ]),

                    Tab::make(__('beneficiary.section.identity.tab.beneficiary'))
                        ->maxWidth('3xl')
                        ->schema([
                            Group::make()
                                ->relationship('beneficiary')
                                ->schema(ViewBeneficiaryIdentity::identitySchemaForOtherPage($this->parent))]),

                    Tab::make(__('monitoring.headings.child_info'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.child_info'))
                                ->visible(fn () => $this->getRecord()->children->isNotEmpty())
                                ->headerActions([
                                    EditAction::make()
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

                                                    DateEntry::make('birthdate')
                                                        ->label(__('monitoring.labels.birthdate')),

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

                    Tab::make(__('monitoring.headings.general'))
                        ->maxWidth('3xl')
                        ->schema([
                            Section::make(__('monitoring.headings.general'))
                                ->headerActions([
                                    EditAction::make()
                                        ->url(self::getParentResource()::getUrl('monitoring.edit_general', [
                                            'parent' => $this->parent,
                                            'record' => $this->getRecord(),
                                        ])),
                                ])
                                ->schema([
                                    Grid::make()
                                        ->schema([
                                            DateEntry::make('admittance_date')
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
                                        ->size(TextSize::Medium),

                                    TextEntry::make('progress')
                                        ->label(__('monitoring.labels.progress')),

                                    TextEntry::make('progress_placeholder')
                                        ->hiddenLabel()
                                        ->default(__('monitoring.headings.observation'))
                                        ->size(TextSize::Medium),

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
                ->size(TextSize::Medium);

            $formFields[] = TextEntry::make($field . '.objection')
                ->label(__('monitoring.labels.objection'));

            $formFields[] = TextEntry::make($field . '.activity')
                ->label(__('monitoring.labels.activity'));

            $formFields[] = TextEntry::make($field . '.conclusion')
                ->label(__('monitoring.labels.conclusion'));
        }

        return $formFields;
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $resource = static::getResource();

        $action->authorize($resource::canDelete($this->getRecord()))
            ->successRedirectUrl(static::getParentResource()::getUrl('monitorings.index', [
                'parent' => $this->parent,
            ]));
    }
}
