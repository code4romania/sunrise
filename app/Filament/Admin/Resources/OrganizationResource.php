<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Enums\OrganizationType;
use App\Filament\Admin\Resources\OrganizationResource\Pages;
use App\Filament\Admin\Resources\OrganizationResource\RelationManagers\UsersRelationManager;
use App\Forms\Components\Location;
use App\Infolists\Components\EnumEntry;
use App\Models\Organization;
use App\Rules\ValidCIF;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

// TODO: remove this
class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static bool $shouldRegisterNavigation = false;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make()
                    ->maxWidth('3xl')
                    ->schema([
                        Infolists\Components\Section::make()
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->label(__('organization.field.name')),

                                Infolists\Components\TextEntry::make('short_name')
                                    ->label(__('organization.field.short_name')),

                                EnumEntry::make('type')
                                    ->label(__('organization.field.type')),

                                Infolists\Components\TextEntry::make('cif')
                                    ->label(__('organization.field.cif')),

                                Infolists\Components\TextEntry::make('main_activity')
                                    ->label(__('organization.field.main_activity')),

                                Infolists\Components\TextEntry::make('phone')
                                    ->label(__('organization.field.phone')),

                                Infolists\Components\TextEntry::make('website')
                                    ->label(__('organization.field.website')),
                            ]),

                        Infolists\Components\Section::make(__('organization.section.location'))
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('address')
                                    ->label(__('organization.field.address'))
                                    ->columnSpanFull(),

                                // Infolists\Components\Location::make()
                                //     ->city()
                                //     ->required(),
                            ]),

                        Infolists\Components\Section::make(__('organization.section.reprezentative'))
                            ->columns(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('reprezentative_name')
                                    ->label(__('organization.field.reprezentative_name')),

                                Infolists\Components\TextEntry::make('reprezentative_email')
                                    ->label(__('organization.field.reprezentative_email')),
                            ]),

                        Infolists\Components\Section::make(__('organization.field.logo'))
                            ->schema([
                                Infolists\Components\SpatieMediaLibraryImageEntry::make('logo')
                                    ->label(__('organization.field.logo'))
                                    ->hiddenLabel()
                                    ->collection('logo')
                                    ->conversion('large')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->maxWidth('3xl')
                    ->schema([
                        Forms\Components\Section::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('organization.field.name')),

                                Forms\Components\TextInput::make('short_name')
                                    ->label(__('organization.field.short_name')),

                                Forms\Components\Select::make('type')
                                    ->label(__('organization.field.type'))
                                    ->options(OrganizationType::options())
                                    ->enum(OrganizationType::class),

                                Forms\Components\TextInput::make('cif')
                                    ->label(__('organization.field.cif'))
                                    ->rule(new ValidCIF),

                                Forms\Components\TextInput::make('main_activity')
                                    ->label(__('organization.field.main_activity')),

                                Forms\Components\TextInput::make('phone')
                                    ->label(__('organization.field.phone'))
                                    ->tel(),

                                Forms\Components\TextInput::make('website')
                                    ->label(__('organization.field.website'))
                                    ->url(),
                            ]),

                        Forms\Components\Section::make(__('organization.section.location'))
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('address')
                                    ->label(__('organization.field.address'))
                                    ->maxLength(200)
                                    ->columnSpanFull()
                                    ->required(),

                                Location::make()
                                    ->city()
                                    ->required(),
                            ]),

                        Forms\Components\Section::make(__('organization.section.reprezentative'))
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('reprezentative_name')
                                    ->label(__('organization.field.reprezentative_name')),

                                Forms\Components\TextInput::make('reprezentative_email')
                                    ->label(__('organization.field.reprezentative_email')),
                            ]),

                        Forms\Components\Section::make(__('organization.field.logo'))
                            ->schema([
                                Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                                    ->label(__('organization.field.logo'))
                                    ->hiddenLabel()
                                    ->image()
                                    ->collection('logo')
                                    ->conversion('large')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->conversion('thumb')
                    ->width(40)
                    ->height(40)
                    ->toggleable(),

                TextColumn::make('name')
                    ->label(__('organization.field.name'))
                    ->searchable(),

                TextColumn::make('short_name')
                    ->label(__('organization.field.short_name'))
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('organization.field.type'))
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->toggleable(),

                TextColumn::make('cif')
                    ->label(__('organization.field.cif'))
                    ->toggleable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('organization.field.type'))
                    ->options(OrganizationType::options())
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
