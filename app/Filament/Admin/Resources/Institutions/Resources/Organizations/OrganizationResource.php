<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Organizations;

use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use App\Filament\Admin\Resources\Institutions\Resources\Organizations\Pages\CreateOrganization;
use App\Filament\Admin\Resources\Institutions\Resources\Organizations\Pages\EditOrganization;
use App\Filament\Admin\Resources\Institutions\Resources\Organizations\Pages\ViewOrganization;
use App\Filament\Admin\Resources\Institutions\Resources\Organizations\Schemas\OrganizationForm;
use App\Filament\Admin\Resources\Institutions\Resources\Organizations\Schemas\OrganizationInfolist;
use App\Filament\Admin\Resources\Institutions\Resources\Organizations\Tables\OrganizationsTable;
use App\Models\Organization;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $parentResource = InstitutionResource::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateOrganization::route('/create'),
            'view' => ViewOrganization::route('/{record}'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }
}
