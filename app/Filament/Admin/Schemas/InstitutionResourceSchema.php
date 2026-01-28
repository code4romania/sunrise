<?php

declare(strict_types=1);

namespace App\Filament\Admin\Schemas;

use App\Enums\AreaType;
use App\Enums\OrganizationType;
use App\Forms\Components\Repeater;
use App\Forms\Components\Select;
use App\Models\City;
use App\Models\County;
use App\Models\Institution;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InstitutionResourceSchema
{
    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->withCount(['organizations', 'beneficiaries', 'users'])
                    ->with(['county', 'city'])
            )
            ->defaultSort('created_at', 'desc')
            ->columns(self::getTableColumns())
            ->filters([])
            ->recordActions(self::getTableActions())
            ->heading(__('institution.headings.all_institutions'))
            ->description(trans_choice('institution.headings.count', Institution::count(), ['count' => Institution::count()]))
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->emptyStateHeading(__('institution.headings.empty_state'))
            ->emptyStateDescription(null);
    }

    public static function getFormSchemaForDetails(): array
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

                    Select::make('county_id')
                        ->label(__('organization.field.county'))
                        ->placeholder(__('organization.placeholders.county'))
                        ->searchable()
                        ->options(County::pluck('name', 'id')->toArray())
                        ->getSearchResultsUsing(fn (string $search): array => County::query()
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray())
                        ->getOptionLabelUsing(fn ($value) => County::find($value)?->name)
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('city_id', null)),

                    Select::make('city_id')
                        ->label(__('organization.field.city'))
                        ->placeholder(__('placeholder.city'))
                        ->searchable()
                        ->options([])
                        ->required()
                        ->disabled(fn (Get $get) => ! $get('county_id'))
                        ->getSearchResultsUsing(function (string $search, Get $get): array {
                            if (! $get('county_id')) {
                                return [];
                            }

                            return City::query()
                                ->where('county_id', (int) $get('county_id'))
                                ->where('name', 'like', "%{$search}%")
                                ->limit(50)
                                ->get()
                                ->pluck('name_with_uat', 'id')
                                ->toArray();
                        })
                        ->getOptionLabelUsing(fn ($value) => City::find($value)?->name_with_uat ?? City::find($value)?->name)
                        ->live(),

                    TextInput::make('address')
                        ->label(__('organization.field.address'))
                        ->placeholder(__('placeholder.address'))
                        ->maxLength(200)
                        ->columnSpanFull()
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

    public static function getFormSchemaForCenters(): array
    {
        return [
            Repeater::make('organizations')
                ->maxWidth('3xl')
                ->hiddenLabel()
                ->columns()
                ->minItems(1)
                ->relationship('organizations')
                ->addActionLabel(__('institution.actions.add_organization'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('institution.labels.center_name'))
                        ->placeholder(__('organization.placeholders.center_name'))
                        ->maxLength(200)
                        ->required(),

                    TextInput::make('short_name')
                        ->label(__('organization.field.short_name'))
                        ->placeholder(__('organization.placeholders.center_short_name'))
                        ->maxLength(50),

                    TextInput::make('main_activity')
                        ->label(__('organization.field.main_activity'))
                        ->placeholder(__('organization.placeholders.main_activity'))
                        ->columnSpanFull()
                        ->maxLength(200)
                        ->required(),

                    SpatieMediaLibraryFileUpload::make('social_service_licensing_certificate')
                        ->label(__('institution.labels.social_service_licensing_certificate'))
                        ->helperText(__('institution.helper_texts.social_service_licensing_certificate'))
                        ->maxSize(config('media-library.max_file_size'))
                        ->collection('social_service_licensing_certificate')
                        ->openable()
                        ->downloadable()
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/*',
                        ])
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('logo')
                        ->label(__('institution.labels.logo_center'))
                        ->helperText(__('institution.helper_texts.logo'))
                        ->maxSize(config('media-library.max_file_size'))
                        ->collection('logo')
                        ->openable()
                        ->downloadable()
                        ->acceptedFileTypes([
                            'image/*',
                        ])
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('organization_header')
                        ->label(__('institution.labels.organization_header'))
                        ->helperText(__('institution.helper_texts.organization_header'))
                        ->maxSize(config('media-library.max_file_size'))
                        ->collection('organization_header')
                        ->openable()
                        ->downloadable()
                        ->acceptedFileTypes([
                            'image/*',
                        ])
                        ->columnSpanFull(),
                ]),
        ];
    }

    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('institution.headings.institution_name')),

            TextColumn::make('county_and_city')
                ->label(__('institution.headings.registered_office')),

            TextColumn::make('organizations_count')
                ->label(__('institution.headings.centers')),

            TextColumn::make('beneficiaries_count')
                ->label(__('institution.headings.cases')),

            TextColumn::make('users_count')
                ->label(__('institution.headings.specialists')),

            TextColumn::make('status')
                ->label(__('institution.headings.status')),
        ];
    }

    public static function getTableActions(): array
    {
        return [
            ViewAction::make()
                ->label(__('general.action.view_details')),
        ];
    }
}
