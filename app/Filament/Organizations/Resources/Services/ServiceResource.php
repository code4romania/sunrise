<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Services;

use App\Filament\Organizations\Resources\Services\Pages\CreateService;
use App\Filament\Organizations\Resources\Services\Pages\EditService;
use App\Filament\Organizations\Resources\Services\Pages\ListServices;
use App\Filament\Organizations\Resources\Services\Pages\ViewService;
use App\Filament\Organizations\Resources\Services\Schemas\ServiceForm;
use App\Filament\Organizations\Resources\Services\Schemas\ServiceInfolist;
use App\Filament\Organizations\Resources\Services\Tables\ServiceTable;
use App\Models\OrganizationService;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ServiceResource extends Resource
{
    protected static ?string $model = OrganizationService::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->serviceWithoutStatusCondition?->name;
    }

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.configurations._group');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAccessToNomenclature() ?? false;
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
        return ServiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ServiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceTable::configure($table);
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
