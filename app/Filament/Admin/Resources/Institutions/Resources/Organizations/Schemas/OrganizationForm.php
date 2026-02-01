<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\Resources\Organizations\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->hiddenLabel()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('institution.labels.center_name'))
                            ->required()
                            ->maxLength(200),
                        TextInput::make('short_name')
                            ->label(__('institution.labels.center_short_name'))
                            ->maxLength(50),
                        TextInput::make('main_activity')
                            ->label(__('organization.field.main_activity'))
                            ->required()
                            ->maxLength(200)
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('social_service_licensing_certificate')
                            ->label(__('institution.labels.social_service_licensing_certificate'))
                            ->helperText(__('institution.helper_texts.social_service_licensing_certificate'))
                            ->collection('social_service_licensing_certificate')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label(__('institution.labels.logo_center'))
                            ->helperText(__('institution.helper_texts.logo'))
                            ->collection('logo')
                            ->acceptedFileTypes(['image/*'])
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('organization_header')
                            ->label(__('institution.labels.organization_header'))
                            ->helperText(__('institution.helper_texts.organization_header'))
                            ->collection('organization_header')
                            ->acceptedFileTypes(['image/*'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
