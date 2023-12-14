<?php

declare(strict_types=1);

namespace App\Filament\Organizations\Pages\Tenancy;

use App\Forms\Components\Location;
use App\Rules\ValidCIF;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditOrganizationProfile extends EditTenantProfile
{
    protected static ?string $slug = 'organization';

    public static function getLabel(): string
    {
        return __('organization.profile');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('organization.field.name')),

                        TextInput::make('short_name')
                            ->label(__('organization.field.short_name')),

                        Select::make('type')
                            ->label(__('organization.field.type')),

                        TextInput::make('cif')
                            ->label(__('organization.field.cif'))
                            ->rule(new ValidCIF),

                        TextInput::make('main_activity')
                            ->label(__('organization.field.main_activity')),

                        TextInput::make('phone')
                            ->label(__('organization.field.phone'))
                            ->tel(),

                        TextInput::make('website')
                            ->label(__('organization.field.website'))
                            ->url(),
                    ]),

                Section::make(__('organization.section.location'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('address')
                            ->label(__('organization.field.address'))
                            ->maxLength(200)
                            ->columnSpanFull()
                            ->required(),

                        Location::make()
                            ->city()
                            ->required(),
                    ]),

                Section::make(__('organization.section.reprezentative'))
                    ->columns(2)
                    ->schema([
                        TextInput::make('reprezentative_name')
                            ->label(__('organization.field.reprezentative_name')),

                        TextInput::make('reprezentative_email')
                            ->label(__('organization.field.reprezentative_email')),
                    ]),

                Section::make(__('organization.field.logo'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label(__('organization.field.logo'))
                            ->hiddenLabel()
                            ->image()
                            ->collection('logo')
                            ->conversion('large')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
