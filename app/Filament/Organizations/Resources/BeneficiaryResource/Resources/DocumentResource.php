<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\Resources;

use App\Filament\Organizations\Resources\BeneficiaryResource\Resources\DocumentResource\Pages;
use App\Filament\Organizations\Schemas\DocumentResourceSchema;
use App\Filament\Organizations\Resources\BeneficiaryResource;
use App\Models\Document;
use Filament\Resources\ParentResourceRegistration;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static bool $shouldRegisterNavigation = false;

    public static ?string $parentResource = BeneficiaryResource::class;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return BeneficiaryResource::asParent()
            ->relationship('documents')
            ->inverseRelationship('beneficiary');
    }

    public static function getRecordTitle(Model|null $record): string|null|Htmlable
    {
        return $record->name;
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentResourceSchema::form($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentResourceSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'view' => Pages\ViewDocument::route('/{record}'),
        ];
    }
}
