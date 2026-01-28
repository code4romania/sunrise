<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Schemas\ServiceResourceSchema;
use App\Filament\Admin\Resources\ServiceResource\Pages\ListServices;
use App\Filament\Admin\Resources\ServiceResource\Pages\ViewService;
use App\Filament\Admin\Resources\ServiceResource\Pages\EditService;
use App\Filament\Admin\Resources\ServiceResource\Pages\CreateService;
use App\Models\Service;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationLabel(): string
    {
        return __('service.label.plural');
    }

    public static function getNavigationParentItem(): ?string
    {
        return __('nomenclature.titles.list');
    }

    public static function getModelLabel(): string
    {
        return __('service.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('service.label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceResourceSchema::form($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceResourceSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'view' => ViewService::route('/{record}'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
