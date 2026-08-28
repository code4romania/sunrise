<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Institutions\RelationManagers;

use App\Filament\Admin\Resources\Institutions\InstitutionResource;
use App\Filament\Admin\Resources\Institutions\Resources\Organizations\OrganizationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrganizationsRelationManager extends RelationManager
{
    protected static string $relationship = 'organizations';

    protected static ?string $relatedResource = OrganizationResource::class;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('institution.headings.center_details');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->heading(__('institution.headings.center_details'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('institution.labels.center_name'))
                    ->searchable(),

                TextColumn::make('short_name')
                    ->label(__('institution.labels.center_short_name')),

                TextColumn::make('institution.short_name')
                    ->label(__('institution.labels.information_center')),

                TextColumn::make('main_activity')
                    ->label(__('organization.field.main_activity'))
                    ->limit(50),

                TextColumn::make('social_service_licensing_certificate')
                    ->label(__('institution.labels.social_service_licensing_certificate'))
                    ->state(fn ($record) => $record->getFirstMedia('social_service_licensing_certificate')?->file_name)
                    ->url(fn ($record) => $record->getFirstMedia('social_service_licensing_certificate')?->getUrl())
                    ->openUrlInNewTab(),

                TextColumn::make('logo')
                    ->label(__('institution.labels.logo_center'))
                    ->state(fn ($record) => $record->getFirstMedia('logo')?->file_name)
                    ->url(fn ($record) => $record->getFirstMedia('logo')?->getUrl())
                    ->openUrlInNewTab(),

                TextColumn::make('organization_header')
                    ->label(__('institution.labels.organization_header'))
                    ->state(fn ($record) => $record->getFirstMedia('organization_header')?->file_name)
                    ->url(fn ($record) => $record->getFirstMedia('organization_header')?->getUrl())
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Action::make('edit_centers')
                    ->label(__('general.action.edit'))
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn () => InstitutionResource::getUrl('edit', ['record' => $this->getOwnerRecord()])),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('general.action.view_details')),
                EditAction::make()
                    ->label(__('general.action.edit')),
                DeleteAction::make()
                    ->label(__('general.action.delete')),
            ]);
    }

    public function form(Schema $schema): Schema
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
                            ->collection('social_service_licensing_certificate')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label(__('institution.labels.logo_center'))
                            ->collection('logo')
                            ->acceptedFileTypes(['image/*'])
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('organization_header')
                            ->label(__('institution.labels.organization_header'))
                            ->collection('organization_header')
                            ->acceptedFileTypes(['image/*'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
