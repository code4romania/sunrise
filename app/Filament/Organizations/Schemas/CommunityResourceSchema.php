<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Schemas;

use App\Tables\Columns\ServiceChipsColumn;
use App\Tables\Filters\ServicesFilter;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CommunityResourceSchema
{
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make()
                            ->extraAttributes([
                                'class' => 'items-center',
                            ])
                            ->schema([
                                TextEntry::make('description')
                                    ->hiddenLabel()
                                    ->size('sm')
                                    ->html(),

                                SpatieMediaLibraryImageEntry::make('logo')
                                    ->hiddenLabel()
                                    ->collection('logo')
                                    ->conversion('large')
                                    ->extraAttributes([
                                        'class' => 'justify-center',
                                    ])
                                    ->extraImgAttributes([
                                        'class' => 'max-w-full max-h-24 md:max-h-48 p-4 ',
                                    ])
                                    ->size('100%')
                                    ->alignCenter(),
                            ]),

                        Flex::make([
                            TextEntry::make('website')
                                ->icon('heroicon-o-link')
                                ->hiddenLabel()
                                ->url(fn (?string $state) => $state)
                                ->openUrlInNewTab(),

                            TextEntry::make('email')
                                ->icon('heroicon-o-envelope')
                                ->hiddenLabel()
                                ->url(
                                    fn (?string $state) => $state !== null
                                        ? "mailto:{$state}"
                                        : null
                                )
                                ->openUrlInNewTab(),

                            TextEntry::make('phone')
                                ->icon('heroicon-o-phone')
                                ->hiddenLabel()
                                ->url(
                                    fn (?string $state) => $state !== null
                                        ? "tel:{$state}"
                                        : null
                                )
                                ->openUrlInNewTab(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('services', 'counties'))
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->filtersLayout(FiltersLayout::AboveContent)
            ->recordActions(self::getTableActions());
    }

    public static function getTableColumns(): array
    {
        return [
            Split::make([
                Stack::make([
                    TextColumn::make('counties.name')
                        ->size('sm')
                        ->color('gray'),

                    TextColumn::make('name')
                        ->searchable()
                        ->size('text-3xl')
                        ->weight('normal')
                        ->extraAttributes([
                            'class' => '-mt-3.5',
                        ]),

                    TextColumn::make('description')
                        ->size('text-sm line-clamp-4')
                        ->formatStateUsing(
                            fn (string $state) => Str::of($state)
                                ->stripTags()
                                ->limit(300, '...')
                        ),

                    ServiceChipsColumn::make('services'),
                ])
                    ->extraAttributes([
                        'class' => 'flex flex-col gap-6',
                    ])
                    ->columnSpan(2),

                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->conversion('large')
                    ->extraImgAttributes([
                        'class' => 'max-w-full max-h-24 md:max-h-48 p-4',
                    ])
                    ->size('100%')
                    ->alignCenter(),

            ])
                ->from('md'),
        ];
    }

    public static function getTableFilters(): array
    {
        return [
            ServicesFilter::make('services')
                ->label(__('organization.filter.service.label'))
                ->columnSpanFull(),

            SelectFilter::make('county')
                ->relationship('counties', 'name')
                ->label(__('organization.filter.county.label'))
                ->placeholder(__('organization.filter.county.placeholder')),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->hidden(),
        ];
    }
}
