<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Resources\Staff;

use App\Filament\Organizations\Resources\Staff\Pages\CreateStaff;
use App\Filament\Organizations\Resources\Staff\Pages\EditStaff;
use App\Filament\Organizations\Resources\Staff\Pages\ListStaff;
use App\Filament\Organizations\Resources\Staff\Pages\ViewStaff;
use App\Filament\Organizations\Resources\Staff\Schemas\StaffForm;
use App\Filament\Organizations\Resources\Staff\Schemas\StaffInfolist;
use App\Filament\Organizations\Resources\Staff\Tables\StaffTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StaffResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'first_name';

    protected static ?string $tenantOwnershipRelationshipName = 'organizations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.configurations._group');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAccessToStaff() ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.configurations.staff');
    }

    public static function getModelLabel(): string
    {
        return __('user.specialist_label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user.specialist_label.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return StaffForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StaffInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaff::route('/'),
            'create' => CreateStaff::route('/create'),
            'view' => ViewStaff::route('/{record}'),
            'edit' => EditStaff::route('/{record}/edit'),
        ];
    }
}
