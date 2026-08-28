<?php

namespace App\Filament\Admin\Resources\Institutions;

use App\Filament\Admin\Resources\Institutions\Pages\CreateInstitution;
use App\Filament\Admin\Resources\Institutions\Pages\EditInstitution;
use App\Filament\Admin\Resources\Institutions\Pages\ListInstitutions;
use App\Filament\Admin\Resources\Institutions\Pages\ViewInstitution;
use App\Filament\Admin\Resources\Institutions\Schemas\InstitutionForm;
use App\Filament\Admin\Resources\Institutions\Schemas\InstitutionInfolist;
use App\Filament\Admin\Resources\Institutions\Tables\InstitutionsTable;
use App\Models\Institution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    public static function getModelLabel(): string
    {
        return __('institution.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('institution.plural');
    }

    public static function getNavigationLabel(): string
    {
        return __('institution.headings.list_title');
    }

    public static function form(Schema $schema): Schema
    {
        return InstitutionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InstitutionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstitutionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            'organizations' => RelationManagers\OrganizationsRelationManager::class,
            'admins' => RelationManagers\AdminsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInstitutions::route('/'),
            'create' => CreateInstitution::route('/create'),
            'view' => ViewInstitution::route('/{record}'),
            'edit' => EditInstitution::route('/{record}/edit'),
        ];
    }
}
