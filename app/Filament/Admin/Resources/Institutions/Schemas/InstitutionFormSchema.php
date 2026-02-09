<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Schemas;

use App\Enums\AreaType;
use App\Enums\OrganizationType;
use App\Models\City;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class InstitutionFormSchema
{
    /**
     * @return array<\Filament\Forms\Components\Component>
     */
    public static function getSchema(): array
    {
        return [
            Section::make()
                ->hiddenLabel()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label(__('organization.field.name'))
                        ->placeholder(__('organization.placeholders.name'))
                        ->maxLength(200)
                        ->required(),

                    TextInput::make('short_name')
                        ->label(__('organization.field.short_name'))
                        ->placeholder(__('organization.placeholders.short_name'))
                        ->maxLength(50),

                    Select::make('type')
                        ->label(__('organization.field.type'))
                        ->placeholder(__('organization.placeholders.type'))
                        ->options(OrganizationType::options())
                        ->enum(OrganizationType::class)
                        ->required(),

                    TextInput::make('cif')
                        ->label(__('organization.field.cif'))
                        ->placeholder(__('organization.placeholders.cif'))
                        ->required(),

                    TextInput::make('main_activity')
                        ->label(__('organization.field.main_activity'))
                        ->placeholder(__('organization.placeholders.main_activity'))
                        ->maxLength(200)
                        ->required(),

                    Select::make('area')
                        ->label(__('organization.field.area'))
                        ->placeholder(__('organization.placeholders.area'))
                        ->options(AreaType::options())
                        ->enum(AreaType::class)
                        ->required(),

                    Select::make('county_id')
                        ->label(__('organization.field.county'))
                        ->placeholder(__('organization.placeholders.county'))
                        ->relationship('county', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),

                    Select::make('city_id')
                        ->label(__('organization.field.city'))
                        ->placeholder(__('placeholder.city'))
                        ->options(function (Get $get): array {
                            $countyId = $get('county_id');

                            if (! $countyId) {
                                return [];
                            }

                            return City::query()
                                ->where('county_id', $countyId)
                                ->get()
                                ->mapWithKeys(fn (City $city) => [$city->id => $city->name_with_uat ?? $city->name])
                                ->toArray();
                        })
                        ->searchable()
                        ->required()
                        ->disabled(fn (Get $get) => ! $get('county_id')),

                    TextInput::make('address')
                        ->label(__('organization.field.address'))
                        ->placeholder(__('placeholder.address'))
                        ->maxLength(200)
                        ->columnSpanFull()
                        ->required(),

                    Grid::make(3)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('representative_person.name')
                                ->label(__('organization.field.representative_name'))
                                ->placeholder(__('organization.placeholders.representative_name'))
                                ->maxLength(50)
                                ->required(),

                            TextInput::make('representative_person.email')
                                ->label(__('organization.field.representative_email'))
                                ->placeholder(__('organization.placeholders.representative_email'))
                                ->maxLength(50)
                                ->email(),

                            TextInput::make('representative_person.phone')
                                ->label(__('organization.field.representative_phone'))
                                ->placeholder(__('organization.placeholders.representative_phone'))
                                ->maxLength(13)
                                ->tel(),
                        ]),

                    Grid::make(3)
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('contact_person.name')
                                ->label(__('organization.field.contact_person'))
                                ->placeholder(__('organization.placeholders.contact_person'))
                                ->maxLength(50)
                                ->required(),

                            TextInput::make('contact_person.email')
                                ->label(__('organization.field.contact_person_email'))
                                ->placeholder(__('organization.placeholders.contact_person_email'))
                                ->maxLength(50)
                                ->required()
                                ->email(),

                            TextInput::make('contact_person.phone')
                                ->label(__('organization.field.contact_person_phone'))
                                ->placeholder(__('organization.placeholders.contact_person_phone'))
                                ->maxLength(13)
                                ->tel()
                                ->required(),
                        ]),

                    TextInput::make('website')
                        ->label(__('organization.field.website'))
                        ->placeholder(__('organization.placeholders.website'))
                        ->maxLength(200)
                        ->url(),

                    SpatieMediaLibraryFileUpload::make('organization_status')
                        ->label(__('institution.labels.organization_status'))
                        ->helperText(__('institution.helper_texts.organization_status'))
                        ->collection('organization_status')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                        ->columnSpanFull()
                        ->required(),

                    SpatieMediaLibraryFileUpload::make('social_service_provider_certificate')
                        ->label(__('institution.labels.social_service_provider_certificate'))
                        ->helperText(__('institution.helper_texts.social_service_provider_certificate'))
                        ->collection('social_service_provider_certificate')
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                        ->columnSpanFull(),
                ]),
        ];
    }
}
