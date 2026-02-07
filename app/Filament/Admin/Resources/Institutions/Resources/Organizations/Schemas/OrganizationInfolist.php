<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Organizations\Schemas;

use App\Models\Organization;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->hiddenLabel()
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('institution.labels.center_name')),
                                TextEntry::make('short_name')
                                    ->label(__('institution.labels.center_short_name')),
                                TextEntry::make('institution.short_name')
                                    ->label(__('institution.labels.information_center')),
                                TextEntry::make('main_activity')
                                    ->label(__('organization.field.main_activity'))
                                    ->columnSpanFull(),
                                TextEntry::make('social_service_licensing_certificate')
                                    ->label(__('institution.labels.social_service_licensing_certificate'))
                                    ->state(fn (Organization $record): ?string => $record->getFirstMedia('social_service_licensing_certificate')?->file_name)
                                    ->url(fn (Organization $record): ?string => $record->getFirstMedia('social_service_licensing_certificate')?->getUrl())
                                    ->openUrlInNewTab(),
                                TextEntry::make('logo')
                                    ->label(__('institution.labels.logo_center'))
                                    ->state(fn (Organization $record): ?string => $record->getFirstMedia('logo')?->file_name)
                                    ->url(fn (Organization $record): ?string => $record->getFirstMedia('logo')?->getUrl())
                                    ->openUrlInNewTab(),
                                TextEntry::make('organization_header')
                                    ->label(__('institution.labels.organization_header'))
                                    ->state(fn (Organization $record): ?string => $record->getFirstMedia('organization_header')?->file_name)
                                    ->url(fn (Organization $record): ?string => $record->getFirstMedia('organization_header')?->getUrl())
                                    ->openUrlInNewTab(),
                            ]),
                    ]),
            ]);
    }
}
