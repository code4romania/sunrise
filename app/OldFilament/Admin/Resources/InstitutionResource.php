<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InstitutionResource\Pages\CreateInstitution;
use App\Filament\Admin\Resources\InstitutionResource\Pages\EditInstitutionCenters;
use App\Filament\Admin\Resources\InstitutionResource\Pages\EditInstitutionDetails;
use App\Filament\Admin\Resources\InstitutionResource\Pages\ListInstitutions;
use App\Filament\Admin\Resources\InstitutionResource\Pages\ViewInstitution;
use App\Filament\Admin\Resources\InstitutionResource\RelationManagers\AdminsRelationManager;
use App\Filament\Admin\Resources\InstitutionResource\RelationManagers\OrganizationsRelationManager;
use App\Filament\Admin\Schemas\InstitutionResourceSchema;
use App\Models\Institution;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

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
        return InstitutionResourceSchema::form($schema);
    }

    public static function table(Table $table): Table
    {
        return InstitutionResourceSchema::table($table);
    }

    public static function getRelations(): array
    {
        return [
            'admins' => AdminsRelationManager::class,
            'organizations' => OrganizationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInstitutions::route('/'),
            'create' => CreateInstitution::route('/create'),
            'view' => ViewInstitution::route('/{record}'),
            'edit_institution_details' => EditInstitutionDetails::route('/{record}/editInstitutionDetails'),
            'edit_institution_centers' => EditInstitutionCenters::route('/{record}/editCenters'),
        ];
    }
}
