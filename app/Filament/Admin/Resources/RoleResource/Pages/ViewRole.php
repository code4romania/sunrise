<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\RoleResource\Pages;

use App\Filament\Admin\Resources\RoleResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function getBreadcrumbs(): array
    {
        return [
            self::$resource::getUrl() => __('nomenclature.titles.list'),
            self::$resource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('nomenclature.actions.edit_role')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()
                ->maxWidth('3xl')
                ->columns()
                ->schema([
                    TextEntry::make('name')
                        ->label(__('nomenclature.labels.role_name'))
                        ->columnSpanFull(),

                    TextEntry::make('case_permissions')
                        ->label(__('nomenclature.labels.case_permissions'))
                        ->listWithLineBreaks(),

                    TextEntry::make('ngo_admin_permissions')
                        ->label(__('nomenclature.labels.ngo_admin_permissions'))
                        ->listWithLineBreaks(),

                ]),

        ]);
    }
}
