<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ResultResource\Pages\CreateResult;
use App\Filament\Admin\Resources\ResultResource\Pages\EditResult;
use App\Filament\Admin\Resources\ResultResource\Pages\ListResults;
use App\Filament\Admin\Schemas\ResultResourceSchema;
use App\Models\Result;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return ResultResourceSchema::form($schema);
    }

    public static function table(Table $table): Table
    {
        return ResultResourceSchema::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResults::route('/'),
            'create' => CreateResult::route('/create'),
            'edit' => EditResult::route('/{record}/edit'),
        ];
    }
}
