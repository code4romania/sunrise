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
                        ->maxLength(200)
                        ->required(),

                    TextInput::make('short_name')
                        ->label(__('organization.field.short_name'))
                        ->maxLength(50),

                    Select::make('type')
                        ->label(__('organization.field.type'))
                        ->options(OrganizationType::options())
                        ->enum(OrganizationType::class)
                        ->required(),

                    TextInput::make('cif')
                        ->label(__('organization.field.cif'))
                        ->rule(new ValidCIF)
                        ->required(),

                    TextInput::make('main_activity')
                        ->label(__('organization.field.main_activity'))
                        ->maxLength(200)
                        ->required(),

                    Location::make()
                        ->city()
                        ->required(),

                    TextInput::make('address')
                        ->label(__('organization.field.address'))
                        ->maxLength(200)
                        ->required(),

                    TextInput::make('phone')
                        ->label(__('organization.field.phone'))
                        ->maxLength(13)
                        ->tel()
                        ->required(),

                    TextInput::make('representative_name')
                        ->label(__('organization.field.representative_name'))
                        ->maxLength(50)
                        ->required(),

                    TextInput::make('representative_email')
                        ->label(__('organization.field.representative_email'))
                        ->maxLength(50)
                        ->email(),

                    TextInput::make('website')
                        ->label(__('organization.field.website'))
                        ->maxLength(200)
                        ->url(),

                    SpatieMediaLibraryFileUpload::make('organization_status')
                        ->label(__('institution.labels.organization_status'))
                        ->helperText(__('institution.helper_texts.organization_status'))
                        ->collection('organization_status')
                        ->columnSpanFull()
                        ->required(),

                    SpatieMediaLibraryFileUpload::make('social_service_provider_certificate')
                        ->label(__('institution.labels.social_service_provider_certificate'))
                        ->helperText(__('institution.helper_texts.social_service_provider_certificate'))
                        ->collection('social_service_provider_certificate')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
