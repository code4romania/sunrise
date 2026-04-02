<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Roles\Schemas;

use App\Enums\AdminPermission;
use App\Enums\CasePermission;
use App\Filament\Admin\Resources\Roles\RoleResource;
use App\Models\Role;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleInfolist
{
    /**
     * @param  \Illuminate\Support\Collection<int, \BackedEnum>|array<\BackedEnum>|\BackedEnum|null  $state
     */
    private static function formatPermissionCollection(mixed $state, string $enumClass): ?string
    {
        if ($state === null) {
            return null;
        }

        $items = is_iterable($state) && ! $state instanceof \BackedEnum
            ? collect($state)
            : collect([$state]);

        return $items
            ->map(function (mixed $permission) use ($enumClass): string {
                if ($permission instanceof \BackedEnum && method_exists($permission, 'getLabel')) {
                    return $permission->getLabel();
                }

                if (is_string($permission) && enum_exists($enumClass)) {
                    $enum = $enumClass::tryFrom($permission);

                    if ($enum !== null && method_exists($enum, 'getLabel')) {
                        return $enum->getLabel();
                    }
                }

                return (string) $permission;
            })
            ->implode(', ');
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('nomenclature.headings.roles'))
                    ->columnSpanFull()
                    ->headerActions([
                        EditAction::make()
                            ->url(fn (Role $record) => RoleResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('nomenclature.labels.role_name')),
                                TextEntry::make('case_manager')
                                    ->label(__('nomenclature.labels.role_case_manager'))
                                    ->formatStateUsing(fn ($state): string => $state ? __('nomenclature.labels.active') : __('nomenclature.labels.inactive'))
                                    ->badge()
                                    ->color(fn ($state): string => $state ? 'success' : 'gray'),
                                TextEntry::make('status')
                                    ->label(__('nomenclature.labels.status'))
                                    ->badge(),
                            ]),
                        Section::make()
                            ->schema([
                                TextEntry::make('case_permissions')
                                    ->label(__('nomenclature.labels.case_permissions'))
                                    ->formatStateUsing(fn ($state) => self::formatPermissionCollection($state, CasePermission::class))
                                    ->placeholder('—'),
                                TextEntry::make('ngo_admin_permissions')
                                    ->label(__('nomenclature.labels.ngo_admin_permissions'))
                                    ->formatStateUsing(fn ($state) => self::formatPermissionCollection($state, AdminPermission::class))
                                    ->placeholder('—'),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }
}
