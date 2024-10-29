<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Enums\OrganizationType;
use App\Filament\Admin\Resources\InstitutionResource;
use App\Forms\Components\Location;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Models\Organization;
use App\Rules\ValidCIF;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\Concerns\HasWizard;
use Filament\Resources\Pages\CreateRecord;

class CreateInstitution extends CreateRecord
{
    use HasWizard;

    protected static string $resource = InstitutionResource::class;

    public function getSteps(): array
    {
        return [
            Step::make('institution_details')
                ->schema([
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
                                ->label(__('organization.field.organization_status'))
                                ->collection('organization_status')
                                ->columnSpanFull(),

                            SpatieMediaLibraryFileUpload::make('social_service_provider_certificate')
                                ->label(__('organization.field.social_service_provider_certificate'))
                                ->collection('social_service_provider_certificate')
                                ->columnSpanFull(),
                        ]),
                ]),

            Step::make('center_details')
                ->schema([
                    Section::make()
                        ->maxWidth('3xl')
                        ->schema([
                            Repeater::make('organizations')
                                ->columns()
                                ->minItems(1)
                                ->relationship('organizations')
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('organization.field.name')),

                                    TextInput::make('short_name')
                                        ->label(__('organization.field.short_name')),

                                    TextInput::make('main_activity')
                                        ->label(__('organization.field.main_activity'))
                                        ->columnSpanFull(),

                                    SpatieMediaLibraryFileUpload::make('social_service_licensing_certificate')
                                        ->label(__('organization.field.social_service_licensing_certificate'))
                                        ->collection('social_service_licensing_certificate')
                                        ->columnSpanFull(),

                                    SpatieMediaLibraryFileUpload::make('logo')
                                        ->label(__('organization.field.logo'))
                                        ->collection('logo')
                                        ->columnSpanFull(),

                                    SpatieMediaLibraryFileUpload::make('organization_header')
                                        ->label(__('organization.field.organization_header'))
                                        ->collection('organization_header')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),

            Step::make('ngo_admin')
                ->schema([
                    Section::make()
                        ->maxWidth('3xl')
                        ->schema([
                            Repeater::make('admins')
                                ->columns()
                                ->minItems(1)
                                ->relationship('admins')
                                ->schema([
                                    TextInput::make('first_name')
                                        ->label(__('organization.field.name')),

                                    TextInput::make('last_name')
                                        ->label(__('organization.field.last_name')),

                                    TextInput::make('email')
                                        ->label(__('organization.field.email')),

                                    TextInput::make('phone')
                                        ->label(__('organization.field.phone')),

                                    Hidden::make('ngo_admin')
                                        ->default(1),
                                ]),
                        ]),
                ]),
        ];
    }

    public function afterCreate()
    {
        $record = $this->getRecord();
        $admins = $record->admins;
        $organizations = $record->organizations;

        $organizations->each(fn (Organization $organization) => $organization->users()->attach($admins->pluck('id')));
    }
}
