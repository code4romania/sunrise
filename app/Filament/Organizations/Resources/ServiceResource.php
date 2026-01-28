<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources;

use App\Filament\Organizations\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Organizations\Resources\ServiceResource\Pages\EditService;
use App\Filament\Organizations\Resources\ServiceResource\Pages\ListServices;
use App\Filament\Organizations\Resources\ServiceResource\Pages\ViewService;
use App\Filament\Organizations\Schemas\ServiceResourceSchema;
use App\Models\OrganizationService;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = OrganizationService::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 32;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.configurations._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('service.headings.navigation');
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

    public static function processInterventionsBeforeSave(?array $interventions): ?array
    {
        return ServiceResourceSchema::processInterventionsBeforeSave($interventions);
    }
}
