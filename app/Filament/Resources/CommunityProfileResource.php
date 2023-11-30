<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityProfileResource\Pages;
use App\Models\CommunityProfile;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CommunityProfileResource extends Resource
{
    protected static ?string $model = CommunityProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static bool $isScopedToTenant = false;

    protected static ?string $slug = 'community';

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.community._group');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.community.network');
    }

    public static function getModelLabel(): string
    {
        return __('beneficiary.label.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('beneficiary.label.plural');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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

                        Split::make([

                            TextEntry::make('website')
                                ->icon('heroicon-o-link')
                                ->hiddenLabel()
                                ->url(fn (string $state) => $state)
                                ->openUrlInNewTab(),

                            TextEntry::make('email')
                                ->icon('heroicon-o-envelope')
                                ->hiddenLabel()
                                ->url(fn (string $state) => "mailto:{$state}")
                                ->openUrlInNewTab(),

                            TextEntry::make('phone')
                                ->icon('heroicon-o-phone')
                                ->hiddenLabel()
                                ->url(fn (string $state) => "tel:{$state}")
                                ->openUrlInNewTab(),

                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                                'class' => 'mt-2.5 mb-6',
                            ]),

                        TextColumn::make('description')
                            ->size('text-sm line-clamp-4')
                            ->formatStateUsing(
                                fn (string $state) => Str::of($state)
                                    ->stripTags()
                                    ->limit(300, '...')
                            ),
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
            ])
            ->filters([
                SelectFilter::make('county')
                    ->relationship('counties', 'name')
                    ->label(__('organization.filter.county.label'))
                    ->placeholder(__('organization.filter.county.placeholder')),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommunityProfiles::route('/'),
            'profile' => Pages\EditCommunityProfile::route('/profile'),
            'view' => Pages\ViewCommunityProfile::route('/{record}'),
        ];
    }
}
