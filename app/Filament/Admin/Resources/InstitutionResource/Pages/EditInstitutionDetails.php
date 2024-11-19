<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Enums\AreaType;
use App\Enums\OrganizationType;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Forms\Components\Location;
use App\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditInstitutionDetails extends EditRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            InstitutionResource::getUrl() => __('institution.headings.list_title'),
            InstitutionResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
    {
        return [
            Section::make()
                ->maxWidth('3xl')
                ->columns()
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
                        ->required(),

                    Location::make()
                        ->city()
                        ->countyLabel(__('organization.field.county'))
                        ->cityLabel(__('organization.field.city'))
                        ->addressLabel(__('organization.field.address'))
                        ->required(),

                    TextInput::make('address')
                        ->label(__('organization.field.address'))
                        ->placeholder(__('organization.placeholders.address'))
                        ->columnSpanFull()
                        ->maxLength(200)
                        ->required(),

                    Grid::make()
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
                        ])->columnSpanFull(),

                    Grid::make()
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
                        ])->columnSpanFull(),

                    TextInput::make('website')
                        ->label(__('organization.field.website'))
                        ->placeholder(__('organization.placeholders.website'))
                        ->maxLength(200)
                        ->url(),

                    SpatieMediaLibraryFileUpload::make('organization_status')
                        ->label(__('institution.labels.organization_status'))
                        ->maxSize(config('media-library.max_file_size'))
                        ->helperText(__('institution.helper_texts.organization_status'))
                        ->collection('organization_status')
                        ->openable()
                        ->downloadable()
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/*',
                        ])
                        ->columnSpanFull()
                        ->required(),

                    SpatieMediaLibraryFileUpload::make('social_service_provider_certificate')
                        ->label(__('institution.labels.social_service_provider_certificate'))
                        ->maxSize(config('media-library.max_file_size'))
                        ->helperText(__('institution.helper_texts.social_service_provider_certificate'))
                        ->collection('social_service_provider_certificate')
                        ->openable()
                        ->downloadable()
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/*',
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }
}
