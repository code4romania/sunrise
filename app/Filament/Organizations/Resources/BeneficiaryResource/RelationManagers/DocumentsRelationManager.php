<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\BeneficiaryResource\RelationManagers;

use App\Filament\Organizations\Resources\BeneficiaryResource\Resources\DocumentResource;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $relatedResource = DocumentResource::class;

    public function form(Schema $schema): Schema
    {
        return DocumentResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return DocumentResource::table($table);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('beneficiary.section.documents.title.page');
    }
}
