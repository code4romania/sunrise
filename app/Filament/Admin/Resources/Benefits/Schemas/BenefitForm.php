<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Benefits\Schemas;

use App\Models\BenefitService;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class BenefitForm
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
                                    ->label(__('nomenclature.labels.benefit_name'))
                                    ->placeholder(__('nomenclature.labels.benefit_name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpanFull(),
                            ]),
                    ]),
                Section::make(__('nomenclature.headings.benefit_types'))
                    ->description(__('nomenclature.helper_texts.benefit_types'))
                    ->schema([
                        Repeater::make('benefitTypes')
                            ->relationship()
                            ->reorderable()
                            ->orderColumn('sort')
                            ->minItems(1)
                            ->addActionLabel(__('nomenclature.actions.add_benefit_type'))
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('nomenclature.labels.benefit_type_name'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpan(2),
                                TextEntry::make('status_display')
                                    ->label(__('nomenclature.labels.active'))
                                    ->state(fn (Get $get): string => $get('status') ? __('nomenclature.labels.active') : __('nomenclature.labels.inactive'))
                                    ->hintAction(
                                        Action::make('deactivate')
                                            ->label(__('nomenclature.actions.change_status.inactivate'))
                                            ->icon('heroicon-o-x-circle')
                                            ->color('danger')
                                            ->requiresConfirmation()
                                            ->modalHeading(__('nomenclature.headings.inactivate_benefit_type_modal'))
                                            ->modalDescription(__('nomenclature.helper_texts.inactivate_benefit_type_modal'))
                                            ->modalSubmitActionLabel(__('nomenclature.actions.change_status.inactivate_benefit_type_modal'))
                                            ->visible(fn (Get $get): bool => (bool) $get('status'))
                                            ->action(function (Set $set): void {
                                                $set('status', 0);
                                            })
                                    )
                                    ->hintAction(
                                        Action::make('activate')
                                            ->label(__('nomenclature.actions.change_status.activate'))
                                            ->icon('heroicon-o-check-circle')
                                            ->color('success')
                                            ->visible(fn (Get $get): bool => ! (bool) $get('status'))
                                            ->action(function (Set $set): void {
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
                                    fn (array $arguments, Repeater $component): bool => static::isBenefitTypeInUse($component, $arguments)
                                )
                            ),
                    ]),
            ]);
    }

    protected static function isBenefitTypeInUse(Repeater $component, array $arguments): bool
    {
        $items = $component->getState();
        $currentItem = $items[$arguments['item']] ?? null;

        if (! isset($currentItem['id'])) {
            return false;
        }

        return BenefitService::query()
            ->whereJsonContains('benefit_types', (int) $currentItem['id'])
            ->exists();
    }
}
