<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Livewire\Nomenclator\BenefitsTable;
use App\Livewire\Nomenclator\ResultsTable;
use App\Livewire\Nomenclator\RolesTable;
use App\Livewire\Nomenclator\ServicesTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class NomenclatorPage extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): ?string
    {
        return __('nomenclature.titles.list');
    }

    public static function getNavigationLabel(): string
    {
        return __('nomenclature.titles.list');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedRectangleStack;
    }

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTab()
                    ->id('nomenclator-tabs')
                    ->tabs([
                        Tab::make(__('nomenclature.headings.service'))
                            ->icon(Heroicon::OutlinedRectangleStack)
                            ->schema([
                                Livewire::make(ServicesTable::class)->lazy(),
                            ]),
                        Tab::make(__('nomenclature.headings.benefits'))
                            ->icon(Heroicon::OutlinedGift)
                            ->schema([
                                Livewire::make(BenefitsTable::class)->lazy(),
                            ]),
                        Tab::make(__('nomenclature.headings.roles'))
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->schema([
                                Livewire::make(RolesTable::class)->lazy(),
                            ]),
                        Tab::make(__('nomenclature.headings.results'))
                            ->icon(Heroicon::OutlinedCheckCircle)
                            ->schema([
                                Livewire::make(ResultsTable::class)->lazy(),
                            ]),
                    ]),
            ]);
    }
}
