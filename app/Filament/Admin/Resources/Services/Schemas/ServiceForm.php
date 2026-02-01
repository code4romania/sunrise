<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Services\Schemas;

use App\Enums\CounselingSheet;
use App\Models\ServiceIntervention;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->columnSpanFull()
                    ->schema([
                        Section::make()
                            ->hiddenLabel()
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('nomenclature.labels.name'))
                                    ->placeholder(__('nomenclature.labels.service_name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpanFull(),
                                Select::make('counseling_sheet')
                                    ->label(__('nomenclature.labels.counseling_sheet'))
                                    ->options(CounselingSheet::options())
                                    ->enum(CounselingSheet::class)
                                    ->placeholder(__('nomenclature.labels.counseling_sheet')),
                            ]),
                    ]),
                Section::make(__('nomenclature.headings.service_intervention'))
                    ->description(__('nomenclature.helper_texts.service_interventions'))
                    ->schema([
                        Repeater::make('serviceInterventions')
                            ->relationship()
                            ->reorderable()
                            ->orderColumn('sort')
                            ->minItems(1)
                            ->addActionLabel(__('nomenclature.actions.add_intervention'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('nomenclature.labels.intervention_name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpan(2),
                                TextEntry::make('status_display')
                                    ->label(__('nomenclature.labels.status'))
                                    ->state(fn (Get $get): string => $get('status') ? __('nomenclature.labels.active') : __('nomenclature.labels.inactive'))
                                    ->hintAction(
                                        Action::make('deactivate')
                                            ->label(__('nomenclature.actions.change_status.inactivate'))
                                            ->icon('heroicon-o-x-circle')
                                            ->color('danger')
                                            ->requiresConfirmation()
                                            ->modalHeading(__('nomenclature.headings.inactivate_intervention_modal'))
                                            ->modalDescription(__('nomenclature.helper_texts.inactivate_intervention_modal'))
                                            ->modalSubmitActionLabel(__('nomenclature.actions.change_status.inactivate_intervention_modal'))
                                            ->visible(fn (Get $get): bool => (bool) $get('status'))
                                            ->action(function (Set $set, ServiceIntervention $record): void {
                                                $record->update(['status' => 0]);
                                                $set('status', 0);
                                            })
                                    )
                                    ->hintAction(
                                        Action::make('activate')
                                            ->label(__('nomenclature.actions.change_status.activate'))
                                            ->icon('heroicon-o-check-circle')
                                            ->color('success')
                                            ->visible(fn (Get $get): bool => ! (bool) $get('status'))
                                            ->action(function (Set $set, ServiceIntervention $record): void {
                                                $record->update(['status' => 1]);
                                                $set('status', 1);
                                            })
                                    ),
                                Hidden::make('status')
                                    ->default(true)
                                    ->dehydrated(),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->deleteAction(
                                fn ($action) => $action->disabled(
                                    fn (array $arguments, Repeater $component): bool => static::isInterventionInUse($component, $arguments)
                                )
                            ),
                    ]),
            ]);
    }

    protected static function isInterventionInUse(Repeater $component, array $arguments): bool
    {
        $items = $component->getState();
        $currentItem = $items[$arguments['item']] ?? null;

        if (! isset($currentItem['id'])) {
            return false;
        }

        $intervention = ServiceIntervention::query()
            ->withCount('allOrganizationIntervention')
            ->find($currentItem['id']);

        return $intervention && $intervention->all_organization_intervention_count > 0;
    }
}
