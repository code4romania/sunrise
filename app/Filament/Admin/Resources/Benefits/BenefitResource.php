<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Benefits;

use App\Filament\Admin\Resources\Benefits\Pages\CreateBenefit;
use App\Filament\Admin\Resources\Benefits\Pages\EditBenefit;
use App\Filament\Admin\Resources\Benefits\Pages\ListBenefits;
use App\Filament\Admin\Resources\Benefits\Pages\ViewBenefit;
use App\Filament\Admin\Resources\Benefits\Schemas\BenefitForm;
use App\Filament\Admin\Resources\Benefits\Schemas\BenefitInfolist;
use App\Filament\Admin\Resources\Benefits\Tables\BenefitsTable;
use App\Models\Benefit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BenefitResource extends Resource
{
    protected static ?string $model = Benefit::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): ?string
    {
        return __('nomenclature.titles.list');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    public static function getModelLabel(): string
    {
        return __('nomenclature.labels.benefit');
    }

    public static function getPluralModelLabel(): string
    {
        return __('nomenclature.headings.benefits');
    }

    public static function getNavigationLabel(): string
    {
        return __('nomenclature.headings.benefits');
    }

    public static function form(Schema $schema): Schema
    {
        return BenefitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BenefitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BenefitsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBenefits::route('/'),
            'create' => CreateBenefit::route('/create'),
            'view' => ViewBenefit::route('/{record}'),
            'edit' => EditBenefit::route('/{record}/edit'),
        ];
    }
}
