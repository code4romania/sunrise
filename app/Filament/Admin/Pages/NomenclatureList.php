<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\ServiceResource\Widgets\NomenclaturesWidget;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class NomenclatureList extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.nomenclature-list';

    public function getTitle(): string|Htmlable
    {
        return __('nomenclature.titles.list');
    }

    public static function getNavigationLabel(): string
    {
        return __('nomenclature.labels.navigation');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NomenclaturesWidget::class,
        ];
    }
}
