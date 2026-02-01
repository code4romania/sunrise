<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BenefitResource\Pages\CreateBenefit;
use App\Filament\Admin\Resources\BenefitResource\Pages\EditBenefit;
use App\Filament\Admin\Resources\BenefitResource\Pages\ListBenefits;
use App\Filament\Admin\Resources\BenefitResource\Pages\ViewBenefit;
use App\Filament\Admin\Schemas\BenefitResourceSchema;
use App\Models\Benefit;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BenefitResource extends Resource
{
    protected static ?string $model = Benefit::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return BenefitResourceSchema::form($schema);
    }

    public static function table(Table $table): Table
    {
        return BenefitResourceSchema::table($table);
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
