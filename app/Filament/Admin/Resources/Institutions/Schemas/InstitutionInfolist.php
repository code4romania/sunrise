<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Schemas;

use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use App\Models\Institution;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class InstitutionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('institution.headings.institution_details'))
                    ->icon(Heroicon::OutlinedBuildingOffice2)
                    ->columnSpanFull()
                    ->headerActions([
                        EditAction::make()
                            ->url(fn (Institution $record) => InstitutionResource::getUrl('edit', ['record' => $record])),
                    ])
                    ->schema([
                        Grid::make(2)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('organization.field.name')),

                                TextEntry::make('short_name')
                                    ->label(__('organization.field.short_name')),

                                TextEntry::make('type')
                                    ->label(__('organization.field.type')),

                                TextEntry::make('cif')
                                    ->label(__('organization.field.cif')),

                                TextEntry::make('main_activity')
                                    ->label(__('organization.field.main_activity')),

                                TextEntry::make('county.name')
                                    ->label(__('organization.field.county')),

                                TextEntry::make('city.name')
                                    ->label(__('organization.field.city')),

                                TextEntry::make('address')
                                    ->label(__('organization.field.address')),

                                TextEntry::make('representative_person.name')
                                    ->label(__('organization.field.representative_name')),

                                TextEntry::make('representative_person.email')
                                    ->label(__('organization.field.representative_email')),

                                TextEntry::make('representative_person.phone')
                                    ->label(__('organization.field.representative_phone')),

                                TextEntry::make('website')
                                    ->label(__('organization.field.website'))
                                    ->url(fn ($state) => filled($state) ? $state : null)
                                    ->openUrlInNewTab(),

                                TextEntry::make('organization_status')
                                    ->label(__('institution.labels.organization_status'))
                                    ->state(fn (Institution $record): ?string => $record->getFirstMedia('organization_status')?->file_name)
                                    ->url(fn (Institution $record): ?string => $record->getFirstMedia('organization_status')?->getUrl())
                                    ->openUrlInNewTab(),

                                TextEntry::make('social_service_provider_certificate')
                                    ->label(__('institution.labels.social_service_provider_certificate'))
                                    ->state(fn (Institution $record): ?string => $record->getFirstMedia('social_service_provider_certificate')?->file_name)
                                    ->url(fn (Institution $record): ?string => $record->getFirstMedia('social_service_provider_certificate')?->getUrl())
                                    ->openUrlInNewTab(),
                            ]),
                    ]),
            ]);
    }
}
