<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\InstitutionResource\Pages;

use App\Filament\Admin\Resources\InstitutionResource;
use App\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditInstitutionCenters extends EditRecord
{
    protected static string $resource = InstitutionResource::class;

    public function form(Form $form): Form
    {
        return $form->schema(self::getSchema());
    }

    public static function getSchema(): array
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
                        ->label(__('institution.labels.center_name')),

                    TextInput::make('short_name')
                        ->label(__('organization.field.short_name')),

                    TextInput::make('main_activity')
                        ->label(__('organization.field.main_activity'))
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('social_service_licensing_certificate')
                        ->label(__('institution.labels.social_service_licensing_certificate'))
                        ->helperText(__('institution.helper_texts.social_service_licensing_certificate'))
                        ->collection('social_service_licensing_certificate')
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('logo')
                        ->label(__('institution.labels.logo_center'))
                        ->helperText(__('institution.helper_texts.logo'))
                        ->collection('logo')
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('organization_header')
                        ->label(__('institution.labels.organization_header'))
                        ->helperText(__('institution.helper_texts.organization_header'))
                        ->collection('organization_header')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
