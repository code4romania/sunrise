<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Results;

use App\Filament\Admin\Resources\Results\Pages\CreateResult;
use App\Filament\Admin\Resources\Results\Pages\EditResult;
use App\Filament\Admin\Resources\Results\Pages\ListResults;
use App\Filament\Admin\Resources\Results\Pages\ViewResult;
use App\Filament\Admin\Resources\Results\Schemas\ResultForm;
use App\Filament\Admin\Resources\Results\Schemas\ResultInfolist;
use App\Filament\Admin\Resources\Results\Tables\ResultsTable;
use App\Models\Result;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    public static function getNavigationGroup(): ?string
    {
        return __('nomenclature.titles.list');
    }

    public static function getModelLabel(): string
    {
        return __('nomenclature.headings.results');
    }

    public static function getPluralModelLabel(): string
    {
        return __('nomenclature.headings.results');
    }

    public static function getNavigationLabel(): string
    {
        return __('nomenclature.headings.results');
    }

    public static function form(Schema $schema): Schema
    {
        return ResultForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ResultInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResultsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResults::route('/'),
            'create' => CreateResult::route('/create'),
            'view' => ViewResult::route('/{record}'),
            'edit' => EditResult::route('/{record}/edit'),
        ];
    }
}
