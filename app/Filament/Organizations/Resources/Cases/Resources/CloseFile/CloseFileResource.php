<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Cases\Resources\CloseFile;

use App\Filament\Organizations\Resources\Cases\CaseResource;
use App\Filament\Organizations\Resources\Cases\Resources\CloseFile\Pages\CreateCloseFile;
use App\Filament\Organizations\Resources\Cases\Resources\CloseFile\Pages\ViewCloseFile;
use App\Models\CloseFile;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CloseFileResource extends Resource
{
    protected static ?string $model = CloseFile::class;

    protected static ?string $slug = 'close-file';

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static bool $shouldRegisterNavigation = false;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return CaseResource::asParent()
            ->relationship('closeFile')
            ->inverseRelationship('beneficiary');
    }

    public static function getModelLabel(): string
    {
        return __('beneficiary.section.close_file.titles.create');
    }

    public static function getPluralModelLabel(): string
    {
        return __('beneficiary.section.close_file.headings.widget');
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record instanceof CloseFile
            ? __('beneficiary.section.close_file.headings.file_details_simple').' #'.$record->getKey()
            : null;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->with(['caseManager.user', 'caseManager.role']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table;
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateCloseFile::route('/create'),
            'view' => ViewCloseFile::route('/{record}'),
        ];
    }
}
