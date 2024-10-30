<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Enums\OrganizationType;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Forms\Components\Location;
use App\Forms\Components\Select;
use App\Rules\ValidCIF;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditInstitutionDetails extends EditRecord
{
    protected static string $resource = InstitutionResource::class;

    protected function getRedirectUrl(): ?string
    {
        return self::$resource::getUrl('view', ['record' => $this->getRecord()]);
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
                        ->label(__('organization.field.name')),

                    TextInput::make('short_name')
                        ->label(__('organization.field.short_name')),

                    Select::make('type')
                        ->label(__('organization.field.type'))
                        ->options(OrganizationType::options())
                        ->enum(OrganizationType::class),

                    TextInput::make('cif')
                        ->label(__('organization.field.cif'))
                        ->rule(new ValidCIF),

                    TextInput::make('main_activity')
                        ->label(__('organization.field.main_activity')),

                    Location::make()
                        ->city()
                        ->required(),

                    TextInput::make('address')
                        ->label(__('organization.field.address'))
                        ->maxLength(200)
                        ->required(),

                    TextInput::make('phone')
                        ->label(__('organization.field.phone'))
                        ->tel(),

                    TextInput::make('reprezentative_name')
                        ->label(__('organization.field.reprezentative_name')),

                    TextInput::make('reprezentative_email')
                        ->label(__('organization.field.reprezentative_email')),

                    TextInput::make('website')
                        ->label(__('organization.field.website'))
                        ->url(),

                    SpatieMediaLibraryFileUpload::make('organization_status')
                        ->label(__('institution.labels.organization_status'))
                        ->helperText(__('institution.helper_texts.organization_status'))
                        ->collection('organization_status')
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('social_service_provider_certificate')
                        ->label(__('institution.labels.social_service_provider_certificate'))
                        ->helperText(__('institution.helper_texts.social_service_provider_certificate'))
                        ->collection('social_service_provider_certificate')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
